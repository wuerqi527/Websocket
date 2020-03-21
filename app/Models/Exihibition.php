<?php

namespace App\Models;

use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exihibition extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'base_exihibitions';

    const
        SORT_DISTANCE = 1, // 距离排序
        SORT_PRICE    = 2; // 价格排序

    // lbs排序类型集
    const SORT_TYPES = [
        self::SORT_DISTANCE => 'distance:1',              // 距离排序
        self::SORT_PRICE    => 'min_price:1',             // 价格排序
    ];

    const
        STATUS_DISABLE = 0,
        STATUS_ENABLE  = 1;

    // orm 排序类型集
    const SORT_ORM_TYPES = [
        // 价格排序
        self::SORT_PRICE    => [
            'min_price' => 'asc',
        ],
        // 距离排序
        self::SORT_DISTANCE => [
            'id' => 'desc',
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

    // skus
    public function getSkusAttribute()
    {
        $weekTexts = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];

        $allSkus = ExihibitionSku::where('exihibition_id', $this->id)->get()->groupBy('used_date');

        $newSkus =[];

        foreach ($allSkus as $date => $skus) {

            $newSkus[] = [
                'date' => $date,
                'week' => $weekTexts[Carbon::parse($date)->dayOfWeek],
                'skus' => $skus,
            ];
        }

        return $newSkus;
    }

    // 距离虚拟属性
    public function getDistanceAttribute($distance)
    {
        return $distance ?? 0;
    }
}
