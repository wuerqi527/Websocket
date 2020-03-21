<?php

namespace App\Services\BaiduLbs;

/**
 * 百度LBS云
 * Web服务API - Geoconv API 坐标转换
 * 坐标转换服务每日请求次数上限为100万次，每次最多支持100个坐标点的转换。
 *
 * @link http://lbsyun.baidu.com/index.php?title=webapi/guide/changeposition
 * @link http://lbsbbs.amap.com/forum.php?mod=viewthread&tid=74&extra=page%3D1
 *
 * @author JiangJian <silverd@sohu.com>
 */

class GeoconvService extends AbstractService
{
    const API_URL = '/geoconv/v1/';

    /**
     * 坐标转换
     *
     * @param string $coords
     *        格式：经度,纬度;经度,纬度…
     *        限制：最多支持100个
     *        格式举例：114.21892734521,29.575429778924;114.21892734521,29.575429778924
     * @param int $from
     *        取值为如下：
     *        1：GPS设备获取的角度坐标，wgs84坐标;
     *        2：GPS获取的米制坐标、sogou地图所用坐标;
     *        3：google地图、soso地图、aliyun地图、mapabc地图和amap高德地图所用坐标=国测局GCJ-02坐标（又称“火星坐标系”）;
     *        4：3中列表地图坐标对应的米制坐标;
     *        5：百度地图采用的经纬度坐标;
     *        6：百度地图采用的米制坐标;
     *        7：mapbar地图坐标;
     *        8：51地图坐标
     * @param int $to
     *        有4种可供选择：
     *        3：国测局（gcj02）坐标
     *        4：3中对应的米制坐标
     *        5：bd09ll(百度经纬度坐标),
     *        6：bd09mc(百度米制经纬度坐标);
     *
     * @return x:经度 y:纬度
     */
    public function convCoords(string $coords, int $from = 3, int $to = 5)
    {
        if (! in_array($from, [1, 2, 3, 4, 5, 6, 7, 8])) {
            throws('坐标来源不合法');
        }

        $params = [
            'coords' => $coords,
            'from'   => $from,
            'to'     => $to,
        ];

        return $this->call('GET', self::API_URL, $params);
    }

    /**
     * 坐标转换（封装外观）
     *
     * @param decimal $longitude
     * @param decimal $latitude
     * @param int $from
     * @param int $to
     * @return array
     */
    public function transCoords(&$longitude, &$latitude, int $from, int $to = 5)
    {
        $longitude = $longitude == 'undefined' ? 0 : $longitude;
        $latitude  = $latitude == 'undefined' ? 0 : $latitude;

        if (! $longitude || ! $latitude) {
            return [$longitude, $latitude];
        }

        // 无需转换
        if (! $from || $from == $to) {
            return [$longitude, $latitude];
        }

        try {
            $result = $this->convCoords($longitude . ',' . $latitude, $from, $to);
            $longitude = $result[0]['x'];
            $latitude  = $result[0]['y'];
        }
        catch (\Throwable $e) {
            // do nothing
        }

        return [$longitude, $latitude];
    }
}
