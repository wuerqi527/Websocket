<?php

/**
 * 小程序Form表单Id
 *
 * @author Cann <imcnny@gmail.com>
 */

namespace App\Models;

class UserMinaFormId extends AbstractModel
{
    protected $table        = 'users_mina_form_ids';
    protected $primaryKey   = 'form_id';
    protected $keyType      = 'string';
    public    $incrementing = false;

    // 不同类型formId可使用的次数
    const TYPES_USE_COUNT = [
        'form_id'   => 1,
        'prepay_id' => 3,
    ];

    public static function setUsable(int $uid, string $formId, string $type)
    {
        if (self::find($formId)) {
            return false;
        }

        return self::create([
            'uid'        => $uid,
            'form_id'    => $formId,
            'type'       => $type,
            'count'      => self::TYPES_USE_COUNT[$type],
            'expired_at' => now()->addDays(7),
        ]);
    }

    public static function getUsable($uid)
    {
        return self::where(['uid' => $uid])
            ->where('expired_at', '>', now())
            ->where('count', '>', 0)
            ->orderBy('expired_at', 'ASC')
            ->value('form_id');
    }

    // 将formId可使用次数-1
    public static function setUsed($formId)
    {
        return self::find($formId)->decrement('count');
    }

    // 将formId可使用次数置为0
    public static function setDisabled($formId)
    {
        return self::find($formId)->update(['count' => 0]);
    }
}
