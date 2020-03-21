<?php

namespace App\Http\Controllers\Api;

use Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Business\OrderBusiness;

class OrderController extends AbstractController
{
    // 订单列表
    public function myOrders(Request $request)
    {
        $request->validate([
            'page' => 'nullable|integer|min:1',
        ]);

        $page = $request->page ?? 1;

        $orders = OrderBusiness::myOrders(Auth::user(), $page);

        return ok('订单列表', compact('orders'));
    }

    // 订单详情
    public function detail(Request $request, Order $order)
    {
        if ($order->uid != Auth::id()) {
            throws('这不是你的订单');
        }

        $order->load(['venue', 'exihibition', 'sku', 'tickets']);

        return ok('订单详情', compact('order'));
    }

    // 创建订单
    public function create(Request $request)
    {
        $posts = $request->validate([
            'sku_id'      => 'required|integer|exists:base_exihibitions_skus,id',
            'goods_count' => 'filled|integer|min:1|max:99',
        ]);

        $user = Auth::user();

        $order = OrderBusiness::createOrder($user, $posts);

        return ok(__FUNCTION__, compact('order'));
    }

    // 取消订单
    public function cancel(Request $request, Order $order)
    {
        $request->validate([
            'reason' => 'nullable|string|max:100',
        ]);

        if ($order->uid != Auth::id()) {
            throws('这不是你的订单');
        }

        OrderBusiness::cancelOrder($order, $request->reason ?? '用户主动取消');

        return ok('取消成功');
    }

    // 获取订单支付信息
    public function payment(Request $request, Order $order)
    {
        $paymentInfo = OrderBusiness::getPaymentInfo(Auth::user(), $order);

        return ok(__FUNCTION__, compact('paymentInfo'));
    }
}
