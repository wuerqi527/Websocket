<?php

namespace App\Models;

class PaymentOrder extends AbstractModel
{
    protected $table = 'payment_orders';

    protected $primaryKey = 'payment_sn';

    protected $keyType = 'string';

    public $incrementing = false;

    const
        // 支付类型
        PAYMENT_STYLE_CLICK     = 1, // 手动支付
        PAYMENT_STYLE_AUTOMATE  = 0; // 免密支付

    const
        // 支付产品类型
        PAYMENT_PRODUCT_EXIHIBITION = 1; // 展会订单

    const
        // 订单支付状态
        PAYMENT_STATUS_DEFAULT   = 0, // 待支付
        PAYMENT_STATUS_PAID      = 1; // 已支付
}
