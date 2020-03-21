<?php
/**
 * 支付类型分发器
 *
 */
namespace App\Business\Payment;

use App\Models\PaymentOrder;

class ProductFactory
{
    const PRODUCT_TYPES = [
        PaymentOrder::PAYMENT_PRODUCT_EXIHIBITION => 'Exihibition',
    ];

    public static function create(int $productType)
    {
        if (! isset(self::PRODUCT_TYPES[$productType])) {
            throws('Invalid PaymentProductType');
        }

        $typeName = self::PRODUCT_TYPES[$productType];

        $className = "App\\Business\\Payment\\Product{$typeName}";

        if (! class_exists($className)) {
            throws('PaymentProduct Not Found: ' . $className);
        }

        return new $className;
    }
}
