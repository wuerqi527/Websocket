<?php

namespace App\Models;

use Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'base_venues';

    const
        SORT_DISTANCE = 1, // 距离排序
        SORT_PRICE    = 2; // 展会数量排序

    // lbs排序类型集
    const SORT_TYPES = [
        self::SORT_DISTANCE => 'distance:1',         // 距离排序
        self::SORT_PRICE    => 'exhibition_count:-1', // 展会数量排序
    ];

    const
        STATUS_DISABLE = 0,
        STATUS_ENABLE  = 1;

    // orm排序类型集
    const SORT_ORM_TYPES = [
        // 距离排序
        self::SORT_DISTANCE => [
            'id' => 'desc',
        ],
        // 展会数量排序
        self::SORT_PRICE    => [
            'exhibition_count' => 'DESC',
        ],
    ];

    // 省
    public function province()
    {
        return $this->belongsTo(Area::class, 'province_code', 'area_code');
    }

    // 城市
    public function city()
    {
        return $this->belongsTo(Area::class, 'city_code', 'area_code');
    }

    // 地区
    public function district()
    {
        return $this->belongsTo(Area::class, 'district_code', 'area_code');
    }

    // 展会列表
    public function exihibitions()
    {
        $now = now();

        return $this->hasMany(Exihibition::class, 'venue_id')
            ->where([
                ['start_sale_at', '<=', $now],
                ['end_sale_at', '>=', $now],
                ['status', '=', Exihibition::STATUS_ENABLE]
            ])
            ->orderBy('id', 'desc');
    }

    // 距离虚拟属性
    public function getDistanceAttribute($distance)
    {
        return $distance ?? 0;
    }

    // 获取展馆下展会数量
    public function getExihibitionCountAttribute($distance)
    {
        $now = now();

        return Exihibition::where([
            ['venue_id', '=', $this->id],
            ['start_sale_at', '<=', $now],
            ['end_sale_at', '>=', $now],
            ['status', '=', Exihibition::STATUS_ENABLE]
        ])->count();
    }
}
