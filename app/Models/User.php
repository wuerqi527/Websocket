<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'api_token',
        'session_key',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'uid');
    }

    public function unpaidOrder()
    {
        return $this->hasOne(Order::class, 'order_sn', 'unpaid_order');
    }
}
