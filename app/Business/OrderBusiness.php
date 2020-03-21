<?php

namespace App\Business;

use Cache;
use QrCode;
use Storage;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Order;
use App\Models\ExihibitionSku;
use App\Models\PaymentOrder;
use App\Helpers\ToolsHelper;
use App\Jobs\SendMinaNotice;

class OrderBusiness
{
    // 我的订单
    public static function myOrders(User $user, $page = 1, $pageSize = 20)
    {
        return Order::with(['venue', 'exihibition'])
            ->where([
                ['uid', '=', $user->id],
                ['status', '=', Order::STATUS_ISSUED]
            ])
            ->orderBy('id', 'DESC')
            ->offset(($page - 1) * $pageSize)
            ->limit($pageSize)
            ->get();
    }

    // 正式提交订单
    public static function createOrder(User $user, array $params)
    {
        $sku = ExihibitionSku::findOrFail($params['sku_id']);

        if ($sku->limit && $params['goods_count'] > $sku->limit) {
            throws('购买数量超出限制');
        }

        if ($params['goods_count'] > $sku->stock) {
            throws('库存不足');
        }

        if ($user->unpaid_order) {
            // 自动取消用户未支付订单
            $user->unpaidOrder->setCanceled('用户创建新订单，自动取消未支付订单');
        }

        // 创建订单
        $order = Order::create([
            'uid'          => $user->id,
            'order_sn'     => ToolsHelper::createSn('Order'),
            'sku_id'       => $sku->id,
            'total_amount' => $sku->price * $params['goods_count'],
            'need_pay'     => $sku->price * $params['goods_count'],
            'goods_count'  => $params['goods_count'],
            'status'       => Order::STATUS_UNPAID,
            'expired_at'   => now()->addMinutes(Order::UNPAID_TTL),
            'sku_info'     => [
                'used_date'   => $sku->used_date,
                'ticket_type' => $sku->ticket_type,
                'price'       => $sku->price,
                'begin_time'  => $sku->begin_time,
                'over_time'   => $sku->over_time,
            ],
        ]);

        // 减少商品库存
        $order->sku->subStock($order->goods_count);

        // 更新用户待付款订单号
        $user->update([
            'unpaid_order' => $order->order_sn,
        ]);

        return $order;
    }

    // 获取订单支付信息，若无需支付则直接请求开票
    public static function getPaymentInfo(User $user, Order $order)
    {
        $paymentInfo = [];

        if ($order->uid != $user->id) {
            throws('这不是你的订单');
        }

        if ($order->payment_sn) {
            throws('该订单已支付');
        }

        // 若订单过支付有效期，将其强制取消
        if (Carbon::parse($order->expired_at)->isPast()) {
            $order->setTimeout();
        }

        if ($order->status == Order::STATUS_CANCELED) {
            throws('您的订单由于长时间未支付已被取消，请重新下单', -2002);
        }

        if ($order->status != Order::STATUS_UNPAID) {
            throws('订单状态不正确');
        }

        // 微信支付过期时间
        $expiredAt = Carbon::parse($order->expired_at);

        // 过期时间与现在时间相差小于5分钟，设置微信支付过期时间为5分钟后
        if (now()->diffInSeconds($expiredAt) <= 300) {
            $expiredAt = now()->addSeconds(300);
        }

        // 判断订单是否为本人操作 并且判断是否符合下单需求
        if ($order->need_pay > 0) {
            $paymentInfo = PaymentBusiness::unifiedOrder([
                'product_type' => PaymentOrder::PAYMENT_PRODUCT_EXIHIBITION,
                'product_id'   => $order->order_sn,
                'amount'       => $order->need_pay,
                'ip'           => request()->ip(),
                'uid'          => $user->id,
                'openid'       => $user->mina_openid,
                'time_start'   => now()->format('YmdHis'),
                'time_expire'  => $expiredAt->format('YmdHis'),
            ]);
        }

        else {

            // 免单支付
            $order->setExempt();
        }

        return $paymentInfo;
    }

    // 通知运营商支付成功(入队列)
    // 预出票（支付成功通知）->出票中->出票结果（成功,失败）
    public static function paidNotifyToSupplier(Order $order)
    {
        // 检测订单状态
        $order->checkStatus([
            Order::STATUS_UNPAID,
            Order::STATUS_CANCELED,
        ]);

        if (! $order->payment_sn && $order->need_pay > 0) {
            throws('该订单未支付');
        }

        // 如果在支付过程中，订单已经过了超时时间，则自动退款
        if ($order->status == Order::STATUS_CANCELED) {

            // 更新用户数据统计
            // 预订成功数-1
            $order->user->stats->decrement('successed_order_count');

            return PaymentBusiness::refund($order->paymentOrder, [
                'product_type' => PaymentOrder::PAYMENT_PRODUCT_EXIHIBITION,
                'product_id'   => $order->order_sn,
                'amount'       => $order->need_pay,
            ]);
        }

        // 变更订单状态（预出票）
        $order->update([
            'status' => Order::STATUS_PRE_ISSUE,
        ]);

        // 推送至队列
        OrderPaidNotifyToSupplier::dispatch($order);

        // 记录订单操作日志
        $order->log('订单支付成功，准备出票', $order->uid);

        return true;
    }

    // 出票
    public static function issueTicket(Order $order)
    {
        // 检测订单状态
        $order->checkStatus(Order::STATUS_PAID);

        $tickets = [];

        // 生成票
        for ($i = 0; $i < $order->goods_count; $i++) {

            // 生成票号
            $ticketNo = ToolsHelper::createRandomSn('TicketNo', '', 4);

            // 生成票号二维码
            $qrCodeStream = QrCode::format('png')->size(200)->generate($ticketNo);

            $imgPath = 'tickets/' . str_random(40);
            Storage::put($imgPath, $qrCodeStream);
            $qrCode = Storage::url($imgPath);

            $tickets[] = [
                'uid'        => $order->uid,
                'order_sn'   => $order->order_sn,
                'ticket_no'  => $ticketNo,
                'qr_code'    => $qrCode,
                'price'      => $order->sku_info['price'],
                'used_date'  => $order->sku_info['used_date'],
                'begin_time' => $order->sku_info['begin_time'],
                'over_time'  => $order->sku_info['over_time'],
                'status'     => Ticket::STATUS_UNUSED,
            ];
        }

        Ticket::insert($tickets);

        $order->setIssued();
    }

    // 取消订单
    public static function cancelOrder(Order $order, string $reason = '')
    {
        // 检测订单状态
        $order->checkStatus(Order::STATUS_UNPAID);

        // 通知运营商取消订单
        $order->stadium->supplier->service->cancelOrder($order);

        // 未支付订单，可直接取消
        if ($order->status == Order::STATUS_UNPAID) {

            $order->setCanceled($reason);

            // 更新用户统计数据
            $order->user->stats->decrement('unpaid_order_count');
        }

        else {
            // 如果通知运营商支付成功接口响应失败，我方会将订单状态置为出票失败
            // 该状态下，用户取消订单，应该把用户支付金额退还用户
            if ($order->payment_sn) {

                // 发起退款
                $result = PaymentBusiness::refund($order->paymentOrder, [
                    'product_type' => PaymentOrder::PAYMENT_PRODUCT_EXIHIBITION,
                    'product_id'   => $order->order_sn,
                    'amount'       => $order->need_pay,
                ]);

                $order->update(['refund_sn' => $result['refundOrder']['refund_sn']]);

                // 将订单置为取消中（正在处理退款）
                $order->setCanceling();
            }

            // 恢复商品库存
            $order->product->addStock($order->goods_count);
        }

        // 返还优惠券
        if ($order->userCoupon) {
            $order->userCoupon->restore();
        }
    }
}
