<?php
/**
 * 支付产品模型-抽象父类
 *
 */
namespace App\Business\Payment;

use App\Models\PaymentOrder;
use App\Models\PaymentRefund;

abstract class Product
{
    protected $name;

    public function getName()
    {
        return $this->name;
    }

    public function preHandlePaymentOrder(array $params)
    {
        return true;
    }

    abstract public function paymentCompleted(PaymentOrder $paymentOrder, $result);

    public function refundCompleted(PaymentRefund $refundOrder, $result)
    {

    }
}
