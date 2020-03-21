<?php

namespace App\Services\BaiduLbs;

/**
 * Web服务API - Place API
 *
 * @link http://lbsyun.baidu.com/index.php?title=webapi/guide/webservice-placeapi
 *
 * @author JiangJian <silverd@sohu.com>
 */

class PlaceService extends AbstractService
{
    const URL_PREFIX = '/place/v2/';

    /**
     * 本地检索(默认全国)
     * @param string $regon
     * @param int $page
     * @param int $pageSize
     */
    public function searchLocal(string $regon = '全国', int $page = 1, int $pageSize = 10)
    {
        $url = self::URL_PREFIX . 'search';

        $params = [
            'region'     => $regon,
            'page_num'   => $page - 1,
            'page_size'  => $pageSize,
            'coord_type' => 2
        ];

        $this->call('GET', $url, $params);
    }

    // 圆形检索

    /**
     * 圆形检索
     * @param string $location
     * @param int $radius
     * @param int $page
     * @param int $pageSize
     */
    public function searchNearBy(string $location, int $radius = 1000 * 50, int $page = 1, int $pageSize = 10)
    {
        $url = self::URL_PREFIX . 'search';

        $params = [
            'location'   => $location,
            'radius'     => $radius,
            'page_num'   => $page - 1,
            'page_size'  => $pageSize,
            'coord_type' => 2
        ];

        $this->call('GET', $url, $params);
    }

    // 矩形检索
    public function searchBound(string $bounds, int $page = 1, int $pageSize = 20)
    {
        $url = self::URL_PREFIX . 'search';

        $params = [
            // 左下角和右上角的经纬度坐标点。2个点用;号分隔
            'bounds'     => $bounds,
            'page_num'   => $page,
            'page_size'  => $pageSize,
            'coord_type' => 2
        ];

        $this->call('GET', $url, $params);
    }

    // 匹配用户输入关键字辅助信息、提示
    public function suggestion(string $q = null, string $region = '全国', float $longitude = null, float $latitude = null)
    {
        $url = self::URL_PREFIX . 'suggestion';

        $params = [
            'q'          => $q,
            'region'     => $region,
            'coord_type' => 2
        ];

        if ($longitude == null && $latitude == null) {
            $params += [
                'location' => $latitude . ',' . $longitude,
            ];
        }

        $this->call('GET', $url, $params);
    }
}
