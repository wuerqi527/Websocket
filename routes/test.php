<?php

use Illuminate\Http\Request;
use App\Models\PaymentOrder;
use App\Models\Ticket;
use App\Models\User;
use App\Business\Payment\ProductFactory;

// 模拟支付成功出票
Route::any('/pay-notify', function (Request $request) {

    $paymentOrder = PaymentOrder::findOrFail($request->payment_sn);

    // 检测订单信息是否异常
    if ($paymentOrder->paid_at) {
        return '已支付';
    }

    $paymentOrder->update([
        'status'         => PaymentOrder::PAYMENT_STATUS_PAID,
        'transaction_id' => 'TEST:' . str_random(32),
        'paid_at'        => now(),
    ]);

    $message = [];

    $product = ProductFactory::create($paymentOrder->product_type);

    $product->paymentCompleted($paymentOrder, $message);

    $paymentOrder->update(['callbacked_at' => now()]);

    return ok();
});

Route::any('/refund-notify', function (Request $request) {

    try {
        $ticket = Ticket::findOrFail($request->ticket_id);

        // 变更退款记录状态
        $ticket->refund->setRefunded($ticket->refund->paymentRefund);

        // 将票置为已作废
        $ticket->setInvalid();

    } catch (\Throwable $e) {
        return response()->output($e->getMessage(), -1);
    }

    return ok();
});

// 生成缩略图
Route::post('/thumb', function (Request $request) {

    $imgurl = 'https://ai-stadium-dev-1251506165.cos.ap-shanghai.myqcloud.com/stadium/2018081010/kdl3ttpqd8EdqpSU3Tb37K0DFaBMABgzwlcdnvF5';
    $cndUrl =  getImgThumbUrl($imgurl, 100, 100);

    dd($cndUrl);
});
