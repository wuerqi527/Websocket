<?php

namespace App\Services\BaiduLbs;

/**
 * 百度LBS云
 * Web服务API - Geocoding API
 *
 * @link http://developer.baidu.com/map/index.php?title=webapi/guide/webservice-geocoding
 *
 * @author JiangJian <silverd@sohu.com>
 */

class GeocodingService extends AbstractService
{

    const API_URL = '/geocoder/v2/';

    /**
     * 将地址转换为经纬度
     *
     * @param $address
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function addrToLoc($address)
    {
        $params = [
            'address' => $address,
            'output'  => 'json',
        ];

        $coord = $this->call('GET', self::API_URL, $params);

        $longitude = $coord['result']['location']['lng'] ?? 0;
        $latitude  = $coord['result']['location']['lat'] ?? 0;

        return [$longitude, $latitude];

    }

    /**
     * 将经纬度转换为地址
     * @param decimal $lng
     * @param decimal $lat
     * @param string $coordtype 坐标的类型，目前支持的坐标类型包括：bd09ll（百度经纬度坐标）、gcj02ll（国测局经纬度坐标）、wgs84ll（ GPS经纬度）
     * @param int $pois 是否显示周边POI
     * @return array
     */
    public function locToAddr($lng, $lat, $coordtype = 'bd09ll', $pois = 0)
    {
        $params = [
            'coordtype' => $coordtype,
            'output'    => 'json',
            'location'  => $lat . ',' . $lng,   // lat<纬度>,lng<经度>
            'pois'      => $pois,
        ];

        $result = $this->call('GET', self::API_URL, $params);

        return $result['result'];
    }

    /**
     *  将经纬度转换为地址（精确到区）
     * @param $lng
     * @param $lat
     * @param string $coordtype
     * @param int $pois
     * @return array 返回省市区
     */
    public function locToCity($lng, $lat, $coordtype = 'bd09ll', $pois = 0)
    {
        $geoInfo = $this->locToAddr($lng, $lat, $coordtype, $pois);

        return $geoInfo['addressComponent'] ?? '';
    }
}
