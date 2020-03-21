<?php

namespace App\Services\BaiduLbs;

/**
 * 百度LBS云 - 云检索服务
 *
 * @link http://developer.baidu.com/map/wiki/index.php?title=lbscloud/api/geosearch
 *
 * @author JiangJian <silverd@sohu.com>
 */

class SearchService extends AbstractService
{
    // 请求组
    const URL_PREFIX = '/geosearch/v3/';

    protected $table;
    protected $filter;

    /**
     * poi周边搜索
     * @read http://lbsyun.baidu.com/index.php?title=lbscloud/api/geosearch
     * @param $geoTblId     int  表id
     * @param $location     array  坐标 [经度， 维度]
     * @param $conditions   array  检索条件，可选字段如下：
     *          $q  sting  搜索关键字
     *          $tags  string 标签
     *          $sortby array   排序字段
     *          $filter array   过滤条件
     *          $filter array  过滤条件
     *          $radius int 搜索半径
     * @param int $page
     * @param int $pageSize
     * @return mixed
     */
    public function getNearby(int $geoTblId, array $location, array $conditions = [], $page = 1, $pageSize = 10)
    {
        $url = self::URL_PREFIX . 'nearby';

        $params = self::buildParams($geoTblId, $conditions, $page, $pageSize);

        $params += [
            // 逗号分隔的经纬度
            'location' => implode(',', $location),
            // 单位为米，默认为1000
            'radius'   => $conditions['radius'] ?? null,
        ];

        return $this->call('GET', $url, $params);
    }

    // poi本地搜索（城市、地区）
    public function getLocal(int $geoTblId, array $conditions = [], int $page = 1, int $pageSize = 10)
    {
        $url = self::URL_PREFIX . 'local';

        $params = self::buildParams($geoTblId, $conditions, $page, $pageSize);

        $params += [
            // 市或区名，如北京市，海淀区。缺省为全国
            'region' => $conditions['region'] ?? null,
        ];

        return $this->call('GET', $url, $params);
    }

    /**
     *  poi矩形检索
     * @param $geoTblName
     * @param array $conditions
     * @param int $page
     * @param int $pageSize
     * @return bool|mixed|string
     */
    public function getBound(int $geoTblId, array $conditions = [], int $page = 1, int $pageSize = 10)
    {
        $url = self::URL_PREFIX . 'bound';

        $params = self::buildParams($geoTblId, $conditions, $page, $pageSize);

        $params += [
            // 左下角和右上角的经纬度坐标点。2个点用;号分隔
            'bounds' => $conditions['bounds'][0][0] ?? 0 . ',' . $conditions['bounds'][0][1] ?? 0 . ';' . $conditions['bounds'][1][0] ?? 0 . ',' . $conditions['bounds'][1][1] ?? 0
        ];

        return $this->call('GET', self::URL_PREFIX . 'bound', $params);
    }

    /**
     * 单条poi详情检索
     * @param $uid 数据id
     * @return bool|mixed|string
     */
    public function getDetail(int $geoTblId, $poiUid)
    {
        $params = [
            'geotable_id' =>$geoTblId,
            'coord_type' => 3  //坐标系 3代表百度经纬度坐标系统 4代表百度墨卡托系统
        ];

        return $this->call('GET', $this->group . 'detail/' . $poiUid, $params);
    }

    protected static function buildParams($geoTblId, array $conditions, int $page, int $pageSize)
    {
        $params = [
            'geotable_id' => $geoTblId,
            'coord_type'  => 3,
            'q'           => $conditions['q'] ?? null,
            'tags'        => $conditions['tags'] ?? null,
            'sortby'      => isset($conditions['sortby']) ? implode('|', (array) $conditions['sortby']) : null,
            'filter'      => isset($conditions['filter']) ? implode('|', (array) $conditions['filter']) : null,
            'page_index'  => $page - 1,     // 索引从0开始
            'page_size'   => $pageSize,
        ];

        return $params;
    }


    // 构造签名
    public static function buildSign(string $url, array $params, string $secretKey, string $method = 'GET')
    {
        if ($method == 'POST') {
            ksort($params);
        }

        return md5(urlencode($url . '?' . http_build_query($params, null, '&', PHP_QUERY_RFC3986) . $secretKey));
    }
}
