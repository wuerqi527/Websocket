<?php

namespace App\Business;

use App\Models\Exihibition;

class ExihibitionBusiness
{
    // 展会列表 from Lbs
    public static function getExihibitionsByLbs(
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
        // 百度LBS展会表表id
        $exihibitionTableId = config('cloud.baidu_lbs.tables.exihibitions.tbl_id');

        // 排序
        $sort = Exihibition::SORT_TYPES[$sort];

        $filter = '';

        if ($areaCode) {

            $area = Area::findOrFail($areaCode);

            // 添加区域筛选条件
            $filter = $area->area_level == Area::LEVEL_CITY ? 'city_code:' . $areaCode : 'district_code:' . $areaCode;
        }

        $pois = LbsBusiness::getNearby($tableId, $coordType, $latitude, $longitude, $keyword, $filter, $sort, $page, $pageSize);

        if (! $pois) {
            throws('Lbs未查询到展会');
        }

        // 加载展馆信息
        foreach ($pois as $poi) {

            try {

                if ($exihibition->status != Exihibition::STATUS_ENABLE) {
                    continue;
                }

                // 加载展会信息
                $exihibition = Exihibition::findOrFail($poi['exihibition_id']);
                $exihibition->distance = $poi['distance'] ?? 0;

                $exihibitions[] = $exihibition;
            }
            catch (\Throwable $e) {
                // do nothing
            }
        }

        return $exihibitions;
    }

    // 查询展会列表 from 本地数据库
    public static function getExihibitionsByLocal(
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
        $sorts = Exihibition::SORT_ORM_TYPES[$sort];

        $exihibitions = Exihibition::where('status', Exihibition::STATUS_ENABLE)

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
        $exihibitions->each->append('distance');

        return $exihibitions;
    }

    // 根据关键词联想场馆名
    public static function getExihibitionNamesByKeyword(string $keyword, string $cityCode)
    {
        if (! $keyword) {
            return [];
        }

        $query = Exihibition::where([
            ['name', 'like', '%' . $keyword . '%'],
            ['status', '=', Exihibition::STATUS_ENABLE],
        ])->limit(20);

        if ($cityCode) {
            $query->where('city_code', $cityCode);
        }

        return $query->pluck('name');
    }
}
