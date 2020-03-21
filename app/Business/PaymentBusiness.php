<?php

namespace App\Business;

use App;
use Log;
use EasyWeChat;
use App\Helpers\ToolsHelper;
use App\Models\PaymentOrder;
use App\Models\PaymentRefund;
use EasyWeChat\Kernel\Support;
use EasyWeChat\Kernel\Support\XML;
use App\Business\Payment\ProductFactory;

class PaymentBusiness
{
    /**
     * 支付统一下单入口
     *
     * @param  array  $params
     * @return array  请求支付页面参数
     */
    public static function unifiedOrder(array $params)
    {
        $product = ProductFactory::create($params['product_type']);

        // 支付前检测
        $product->preHandlePaymentOrder($params);

        $paymentOrder = PaymentOrder::create([
            'payment_sn'   => ToolsHelper::createSn('payment'),
            'uid'          => $params['uid'],
            'openid'       => $params['openid'],
            'amount'       => $params['amount'],
            'payment_type' => PaymentOrder::PAYMENT_STYLE_CLICK,
            'product_type' => $params['product_type'],
            'product_id'   => $params['product_id'],
            'product_name' => $product->getName(),
            'status'       => PaymentOrder::PAYMENT_STATUS_DEFAULT,
        ]);

        $payService = EasyWeChat::payment();

        $result = $payService->order->unify([
            'body'             => $paymentOrder['product_name'],
            'out_trade_no'     => $paymentOrder['payment_sn'],
            'total_fee'        => $paymentOrder['amount'] * 100,
            'spbill_create_ip' => $params['ip'],
            'trade_type'       => 'JSAPI',
            'openid'           => $params['openid'],
            'time_start'       => $params['time_start'],
            'time_expire'      => $params['time_expire'],
        ]);

        if (
            $result['return_code'] != 'SUCCESS' ||
            $result['result_code'] != 'SUCCESS'
        ) {
            // 错误信息
            $errorMsg = '微信统一下单异常：' . ($result['err_code_des'] ?? $result['return_msg']);

            // 记录日志
            Log::channel('payment_error')->error($errorMsg, $result);

            throws($errorMsg);
        }

        $paymentOrder->update(['prepay_id' => $result['prepay_id']]);

        // 给客户端构造支付配置信息
        $params = [
            'appId'     => $result['appid'],
            'nonceStr'  => uniqid(),
            'package'   => 'prepay_id=' . $result['prepay_id'],
            'signType'  => 'MD5',
            'timeStamp' => time(),
        ];

        return [
            'timestamp'  => $params['timeStamp'],
            'nonce_str'  => $params['nonceStr'],
            'package'    => $params['package'],
            'sign_type'  => $params['signType'],
            'pay_sign'   => Support\generate_sign($params, $payService['config']->key),
            'prepay_id'  => $result['prepay_id'],
            'payment_sn' => $paymentOrder->payment_sn,
        ];
    }

    /**
     * 统一申请退款接口
     *
     * @param obj   $paymentOrder 支付订单
     * @param array $extra 扩展字段，可传退款金额
     * @see EasyWeChat https://www.easywechat.com/docs/master/zh-CN/payment/refund
     * @see 微信支付    https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_4
     *
     * @return  退款结果
     */
    public static function refund(PaymentOrder $paymentOrder, array $extra = [])
    {
        $refundOrder = PaymentRefund::create([
            'refund_sn'      => ToolsHelper::createSn('refund'),
            'uid'            => $paymentOrder->uid,
            'amount'         => $extra['amount'] ?? $paymentOrder->amount,
            'payment_sn'     => $paymentOrder->payment_sn,
            'product_id'     => $extra['product_id'] ?? $paymentOrder->product_id,
            'product_type'   => $extra['product_type'] ?? $paymentOrder->product_type,
            'transaction_id' => $paymentOrder->transaction_id,
            'status'         => PaymentRefund::STATUS_DEFAULT,
            'reason'         => $extra['reason'] ?? '',
        ]);

        // 退款参数
        $params = [
            $paymentOrder->transaction_id,
            $refundOrder->refund_sn,
            $paymentOrder->amount * 100,
            $refundOrder->amount * 100,
            [
                'refund_desc' => $refundOrder->reason,
                'notify_url'  => config('app.url') . '/notify/refund-notify',
            ],
        ];

        $errorMsg = '';
        $result   = [];

        try {

            // 发起退款
            $result = EasyWeChat::payment()->refund->byTransactionId(...$params);

            if (
                $result['return_code'] != 'SUCCESS' ||
                $result['result_code'] != 'SUCCESS'
            ) {
                // 错误信息
                $errorMsg = '微信发起退款异常：' . ($result['err_code_des'] ?? $result['return_msg']);
            }

        } catch (\Throwable $e) {
            $errorMsg = '微信发起退款异常：' . $e->getMessage() . ' ErrorCode:' . $e->getCode();
        }

        if ($errorMsg) {
            // 记录日志
            Log::channel('refund_error')->error($errorMsg, [
                'params' => $params,
                'result' => $result,
            ]);
        }

        return [
            'result'      => $result,
            'refundOrder' => $refundOrder,
        ];
    }

    /**
     * 订单查询接口
     *
     * @param transaction_id $transactionId 微信的订单号
     *
     * @see   https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_2
     * @see   https://www.easywechat.com/docs/master/zh-CN/payment/order
     *
     * @return array
     */
    public static function getOrder(string $transactionId)
    {
        return EasyWeChat::payment()->order->queryByTransactionId($transactionId);
    }
}
