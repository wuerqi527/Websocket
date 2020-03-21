<?php

namespace App\Business;

use App\Models\Venue;

class VenueBusiness
{
    // 查询场馆列表 from Lbs
    public static function getVenuesByLbs(
        string $keyword,
        string $areaCode,
        int $sort,
        int $coordType,
        float $latitude,
        float $longitude,
        int $page,
        int $pageSize = 20
    )
    {
        // 百度LBS场馆表表id
        $tableId = config('cloud.baidu_lbs.tables.venues.tbl_id');

        // 排序
        $sort = Venue::SORT_TYPES[$sort];

        $filter = '';

        if ($areaCode) {

            $area = Area::findOrFail($areaCode);

            // 添加区域筛选条件
            $filter = $area->area_level == Area::LEVEL_CITY ? 'city_code:' . $areaCode : 'district_code:' . $areaCode;
        }

        $pois = LbsBusiness::getNearby($tableId, $coordType, $latitude, $longitude, $keyword, $filter, $sort, $page, $pageSize);

        if (! $pois) {
            throws('Lbs未查询到场馆');
        }

        $venues = [];

        // 加载展馆信息
        foreach ($pois as $poi) {

            try {
                if ($poi['status'] != Venue::STATUS_ENABLE) {
                    continue;
                }

                // 加载展馆信息
                $venue = Venue::findOrFail($poi['venue_id']);
                $venue->distance = $poi['distance'] ?? 0;
                $venue->append('exihibition_count');
                $venue->load('exihibitions');

                $venues[] = $venue;
            }
            catch (\Throwable $e) {
                // do nothing
            }
        }

        return $venues;
    }

    // 查询场馆列表from本地数据库
    public static function getVenuesByLocal(
        string $keyword,
        string $areaCode,
        int $sort,
        int $coordType,
        float $latitude,
        float $longitude,
        int $page,
        int $pageSize = 20
    )
    {
        // 排序
        $sorts = Venue::SORT_ORM_TYPES[$sort];

        $venues = Venue::where('status', Venue::STATUS_ENABLE)

            ->with('exihibitions:id,cover_url')

            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('keywords', 'like', '%' . $keyword . '%');
            })

            ->when($areaCode, function ($query) use ($areaCode) {
                return $query->where(function ($query) use ($areaCode) {
                    $query->where('city_code', $areaCode)->orWhere('district_code', $areaCode);
                });
            })

            ->when($sort, function ($query) use ($sorts) {
                foreach ($sorts as $field => $sort) {
                    $query->orderBy($field, $sort);
                }
                return $query;
            })

            ->offset(($page - 1) * $pageSize)
            ->limit($pageSize)
            ->get();

        // 加载展馆信息
        $venues->each->append(['distance', 'exihibition_count']);

        return $venues;
    }

    // 根据关键词联想场馆名
    public static function getVenueNamesByKeyword(string $keyword, string $cityCode)
    {
        if (! $keyword) {
            return [];
        }

        $query = Venue::where([
            ['name', 'like', '%' . $keyword . '%'],
            ['status', '=', Venue::STATUS_ENABLE],
        ])->limit(20);

        if ($cityCode) {
            $query->where('city_code', $cityCode);
        }

        return $query->pluck('name');
    }
}
