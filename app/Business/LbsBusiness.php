<?php

namespace App\Business;

class LbsBusiness
{
    // Lbs 查找附近的目标
    public static function getNearby(
        string $tableId,
        int $coordType,
        float $latitude,
        float $longitude,
        string $q = '',
        string $filter = '',
        string $sort = '',
        int $page = 1,
        int $pageSize = 20
    )
    {
        // 只筛选启用的po
        $filter = implode('|', array_filter([$filter, 'status:1']));

        // 只搜可用的和目标类型 和排序
        $conditions = array_filter(compact('filter', 'sort', 'q'));

        // 如果有经纬度
        if ($longitude && $latitude) {

            // 将其他坐标系转化为百度坐标系
            app('lbs')->geoconv->transCoords($longitude, $latitude, $coordType);

            $conditions += [
                'radius' => 25000 * 1000, // 检索半径（米）
            ];

            $result = app('lbs')->search->getNearby($tableId, [$longitude, $latitude], $conditions, $page, $pageSize);
        }

        // 没有经纬度，默认全范围检索
        else {

            $conditions += [
                'region' => '全国',
            ];

            $result = app('lbs')->search->getLocal($tableId, $conditions, $page, $pageSize);
        }

        return $result['contents'];
    }
}
