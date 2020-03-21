<?php

// 对外开放接口
use Illuminate\Http\Request;

Route::group([
    'namespace' => 'Notify',
], function () {

    // 支付回调通知
    Route::post('payment-notify', 'WechatController@paymentNotify');

    // 退款回调通知
    Route::post('refund-notify', 'WechatController@refundNotify');
});
