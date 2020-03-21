<?php

namespace App\Models;

class Ticket extends AbstractModel
{
    protected $table = 'base_orders_tickets';

    // 票状态
    const
        // 未使用
        STATUS_UNUSED  = 0,
        // 已使用
        STATUS_USED    = 10,
        // 已过期
        STATUS_EXPIRED = 20;

    // 所属订单
    public function order()
    {
        return $this->hasOne(Order::class, 'order_sn', 'order_sn');
    }

    // 所属场馆
    public function exihibition()
    {
        return $this->hasOne(Exihibition::class, 'id', 'exihibition_id');
    }

    // 用户
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'uid');
    }

    // 票券置为已使用
    public function setUsed()
    {
        // 检测订单状态
        $this->checkStatus(self::STATUS_UNUSED);

        $this->update([
            'status'   => self::STATUS_USED,
            'used_at' => now(),
        ]);

        // 减少一张可用票数量
        $this->order->disableOneTicket();
    }

    // 票券置为已过期
    public function setExpired()
    {
        // 检测订单状态
        $this->checkStatus(self::STATUS_UNUSED);

        $this->update([
            'status' => self::STATUS_EXPIRED,
        ]);
    }

    // 检测票券状态
    public function checkStatus($status)
    {
        if (! in_array($this->status, (array) $status)) {
            throws('票状态不正确');
        }
    }
}
