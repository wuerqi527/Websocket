<?php

namespace App\Models;

use Carbon\Carbon;

class Order extends AbstractModel
{
    protected $table = 'base_orders';

    protected $primaryKey = 'order_sn';

    protected $casts = [
        'total_amount' => 'float',
        'need_pay'     => 'float',
        'sku_info'     => 'array',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    // 订单状态
    const
        // 默认状态（待支付）
        STATUS_UNPAID    = 0,
        // 支付成功
        STATUS_PAID      = 10,
        // 已出票
        STATUS_ISSUED    = 20,
        // 已完成
        STATUS_FINISHED  = 97,
        // 取消中
        STATUS_CANCELING = 98,
        // 订单已取消
        STATUS_CANCELED  = 99;

    // 未支付订单必须在X分钟内支付
    const UNPAID_TTL = 15;

    // 所属展馆
    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    // 所属展会
    public function exihibition()
    {
        return $this->belongsTo(Exihibition::class, 'exihibition_id');
    }

    // 支付订单
    public function paymentOrder()
    {
        return $this->hasOne(PaymentOrder::class, 'payment_sn', 'payment_sn');
    }

    // 退款订单
    public function paymentRefund()
    {
        return $this->hasOne(PaymentRefund::class, 'refund_sn', 'refund_sn');
    }

    // sku
    public function sku()
    {
        return $this->belongsTo(ExihibitionSku::class, 'sku_id');
    }

    // 订单票列表
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'order_sn', 'order_sn');
    }

    // 用户信息
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'uid');
    }

    // 订单超时未支付
    public function setTimeout()
    {
        $this->checkStatus(self::STATUS_UNPAID);

        $message = '订单超时未支付，自动取消订单';

        // 取消订单
        $this->setCanceled($message);

        // 记录订单操作日志
        $this->log($message);
    }

    // 订单置为支付免单
    public function setExempt()
    {
        $this->checkStatus(self::STATUS_UNPAID);

        $this->update([
            'status' => self::STATUS_PAID,
        ]);

        // 清除用户待付款订单
        $this->user->update([
            'unpaid_order' => '',
        ]);

        // 记录订单操作日志
        $this->log('订单已支付（0元）');
    }

    // 订单置为支付成功
    public function setPaid(PaymentOrder $paymentOrder)
    {
        if (! $paymentOrder->paid_at) {
            throws('该支付未完成');
        }

        // 更新订单支付订单号
        $this->update([
            'status'     => self::STATUS_PAID,
            'payment_sn' => $paymentOrder->payment_sn,
        ]);

        // 清除用户待付款订单
        $this->user->update([
            'unpaid_order' => '',
        ]);

        // 记录订单操作日志
        $this->log('订单已支付');
    }

    // 订单置为出票成功
    public function setIssued()
    {
        $this->checkStatus(self::STATUS_PAID);

        $this->update([
            'status' => self::STATUS_ISSUED,
        ]);

        // 清除用户待付款订单
        $this->user->update([
            'unpaid_order' => '',
        ]);

        // 记录订单操作日志
        $this->log('订单已支付');
    }

    // 订单退款成功
    public function setRefunded(PaymentRefund $refundOrder)
    {
        $this->checkStatus(self::STATUS_CANCELING);

        // 记录订单日志
        $this->log('订单退款成功');
    }

    // 订单置为已取消
    public function setCanceled(string $reason = '')
    {
        $this->checkStatus([
            self::STATUS_UNPAID,
            self::STATUS_CANCELING,
        ]);

        $this->update([
            'status'     => self::STATUS_CANCELED,
            'cancel_msg' => $reason,
        ]);

        // 更新用户未支付订单号
        $this->user->update([
            'unpaid_order' => '',
        ]);

        // 释放库存
        $this->sku->addStock($this->goods_count);

        // 记录操作日志
        $this->log('订单已取消，理由：' . $reason, $this->uid);
    }

    // 订单日志
    public function log(string $content, int $uid = 0)
    {
        return OrderLog::create([
            'order_sn' => $this->order_sn,
            'uid'      => $uid,
            'content'  => $content,
        ]);
    }

    // 检测订单状态
    public function checkStatus($status)
    {
        if (! in_array($this->status, (array) $status)) {
            throws('订单状态异常，请重新操作');
        }
    }
}
