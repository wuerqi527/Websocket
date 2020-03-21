<?php

/**
 * 总订单退款回调类
 *
 * @author 527
 */

namespace App\Business\Payment;

use App\Models\PaymentOrder;
use App\Models\PaymentRefund;
use App\Models\Order;
use App\Business\OrderBusiness;

class ProductExihibition extends Product
{
    protected $name = '展会订单支付';

    public function paymentCompleted(PaymentOrder $paymentOrder, $result)
    {
        $order = Order::findOrFail($paymentOrder['product_id']);

        $order->setPaid($paymentOrder);

        // 出票
        OrderBusiness::issueTicket($order);
    }

    public function refundCompleted(PaymentRefund $refundOrder, $result)
    {
        $order = Order::findOrFail($refundOrder->product_id);

        // 退款成功
        $order->setRefunded($refundOrder);

        // 取消订单
        $order->setCanceled('退款成功，强制取消订单');
    }
}
