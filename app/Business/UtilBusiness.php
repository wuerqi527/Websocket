<?php

namespace App\Business;

use App\Models\Area;
use App\Models\Banner;
use App\Models\HotKeyword;

class UtilBusiness
{
    // 获取banner列表
    public static function getBanners(string $cityCode)
    {
        $now = now();

        $banners = Banner::where(['status' => Banner::STATUS_ENABLED])
            ->where([
                ['start_time', '<=', $now],
                ['end_time', '>=', $now],
                ['city_code', '=', $cityCode],
            ])
            ->orderBy('display_order', 'desc')
            ->get();

        return $banners;
    }

    public static function getCities()
    {
        // 获取市级城市列表
        $cities =  Area::where([
            'area_level' => Area::LEVEL_CITY,
        ])
        ->orderBy('first_letter', 'ASC')
        ->get();

        $allCities = $cities->groupBy(function ($item) {
            return $item->first_letter;
        });

        // 获取热门城市列表
        $hotCities = $cities->where('is_hot', 1)->values();

        return [
            'hot_cities' => $hotCities,
            'cities'     => $allCities,
        ];
    }

    // 根据坐标获取所在城市
    public static function getCityByCoordinate(float $longitude, float $latitude)
    {
        // 城市名称
        $cityName  = '';

        if ($longitude && $latitude) {

            try {
                $cityInfo = app('lbs')->geocoding->locToCity($longitude, $latitude, 'gcj02ll');
                $cityName = $cityInfo['city'];
            } catch (\Throwable $e) {
                // do nothing
            }
        }

        // 获取市级城市
        $city = Area::where([
            'area_name'  => $cityName,
            'area_level' => Area::LEVEL_CITY,
        ])->first();

        if (! $city) {
            throws('未匹配到城市', -2020);
        }

        return $city;
    }
}
