<?php

namespace App\Http\Controllers\Notify;

use Log;
use Validator;
use EasyWeChat;
use Illuminate\Http\Request;
use App\Models\PaymentOrder;
use App\Models\PaymentRefund;
use App\Models\UserMinaFormId;
use App\Business\PaymentBusiness;
use EasyWeChat\Kernel\Support\XML;
use App\Business\Payment\ProductFactory;

class WechatController extends AbstractController
{
    /**
     * 支付结果回调通知接口
     */
    public function paymentNotify(Request $request)
    {
        // $message 微信推送过来的通知信息，是一个数组
        // @see https://www.easywechat.com/docs/master/zh-CN/payment/notify
        $callback = function ($message, $fail) {

            if (
                $message['return_code'] != 'SUCCESS' ||
                $message['result_code'] != 'SUCCESS'
            ) {
                // 错误信息
                $errorMsg = '微信统一下单异常：' . ($message['err_code_des'] ?? $message['return_msg']);

                // 记录日志
                Log::channel('payment_error')->error($errorMsg, $message);

                return $fail($errorMsg);
            }

            $paymentOrder = PaymentOrder::findOrFail($message['out_trade_no']);

            // 检测订单信息是否异常
            if (
                $paymentOrder['paid_at']
                || bcmul($paymentOrder->amount, 100) != $message['total_fee']
            ) {
                return true;
            }

            $paymentOrder->update([
                'status'         => PaymentOrder::PAYMENT_STATUS_PAID,
                'transaction_id' => $message['transaction_id'],
                'paid_at'        => now(),
            ]);

            // 支付成功回调
            $product = ProductFactory::create($paymentOrder['product_type']);

            $product->paymentCompleted($paymentOrder, $message);

            $paymentOrder->update(['callbacked_at' => now()]);

            // 记录prepay_id用于发送模板消息
            UserMinaFormId::setUsable($paymentOrder->uid, $paymentOrder->prepay_id, 'prepay_id');

            return true;
        };

        try {
            $response = EasyWeChat::payment()->handlePaidNotify($callback);
        } catch (\Throwable $e) {
            // TODO
            // 日志
            throws($e->getMessage());
        }

        return $response;
    }

    /**
     * 退款结果回调通知
     */
    public function refundNotify()
    {
        $callback = function ($message, $reqInfo, $fail) {

            if ($message['return_code'] != 'SUCCESS') {

                // 记录日志
                Log::channel('refund_error')->error(
                    '微信退款回调异常：' . $message['return_msg'],
                    $message
                );

                return $fail('退款回调通知异常');
            }

            $refundOrder = PaymentRefund::findOrFail($reqInfo['out_refund_no']);

            // 防止重复退款
            if ($refundOrder['status'] == PaymentRefund::STATUS_REFUNDED) {
                return true;
            }

            $product = ProductFactory::create($refundOrder['product_type']);

            $wxNotifyStatus = $reqInfo['refund_status'];

            if ($wxNotifyStatus != 'SUCCESS') {

                // 更新退款状态
                $refundOrder->update([
                    'status' => ($wxNotifyStatus == 'CHANGE') ? (PaymentRefund::STATUS_CHANGE) : (PaymentRefund::STATUS_CLOSE),
                ]);

                // 记录日志
                Log::channel('refund_error')->error(
                    '微信退款状态异常，refund_status：' . $wxNotifyStatus,
                    [
                        $message,
                        $reqInfo
                    ]
                );

                return true;
            }

            $refundOrder->update([
                'status'      => PaymentRefund::STATUS_REFUNDED,
                'refund_id'   => $reqInfo['refund_id'],
                'refunded_at' => $reqInfo['success_time'],
            ]);

            $product->refundCompleted($refundOrder, $reqInfo);

            return true;
        };

        // 其中 $message['req_info'] 获取到的是加密信息
        // $reqInfo 为 $message['req_info'] 解密后的信息
        // @see https://www.easywechat.com/docs/master/zh-CN/payment/notify
        try {
            $response = EasyWeChat::payment()->handleRefundedNotify($callback);
        } catch (\Throwable $e) {
            // TODO
            // 日志
            throws($e->getMessage());
        }

        return $response;
    }
}
