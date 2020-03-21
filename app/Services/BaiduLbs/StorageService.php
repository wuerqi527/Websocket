<?php

namespace App\Services\BaiduLbs;

/**
 * 百度LBS云 - 云存储 服务
 *
 * @link 接口文档 http://developer.baidu.com/map/wiki/index.php?title=lbscloud/api/geodata
 * @link 管理后台 http://lbsyun.baidu.com/datamanager/datamanage
 *
 * @author JiangJian <silverd@sohu.com>
 */

class StorageService extends AbstractService
{
    // 请求组
    const URL_PREFIX = '/geodata/v3/';

    /**
     * 创建数据（create poi）接口
     * @param $geoTableId  int   表id
     * @param $postData    array 提交的请求数据
     *          latitude string 纬度
     *          longitude string 经度
     *          title string 标题
     *          address string 地址
     *          tags string 标签
     * @param $coordType  int 坐标类型
     *          1：GPS经纬度坐标
     *          2：国测局加密经纬度坐标
     *          3：百度加密经纬度坐标
     *          4：百度加密墨卡托坐标
     * @return array
     */
    public function createPoi(int $geoTableId, array $postData, int $coordType = 3)
    {
        $url = self::URL_PREFIX . 'poi/create';

        $params = [
            'geotable_id' => $geoTableId,
            'coord_type'  => $coordType,
            'latitude'    => $postData['latitude'],
            'longitude'   => $postData['longitude'],
            // 以下选填
            'title'       => $postData['title'] ?? null,
            'address'     => $postData['address'] ?? null,
            'tags'        => $postData['tags'] ?? null,
        ];

        // 自定义字段
        if (isset($postData['custom_cols'])) {
            $params += $postData['custom_cols'];
        }

        return $this->call('POST', $url, $params);
    }

    /**
     * 修改数据（poi）接口
     * @param $geoTableId int
     * @param $pk array ['field' => value]
     * @param array $postData
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function updatePoi(int $geoTableId, array $pk, array $postData, int $coordType = 3)
    {
        $url = self::URL_PREFIX . 'poi/update';

        $params = [
            'geotable_id' => $geoTableId,
            'coord_type'  => $coordType,
            // 以下选填
            'latitude'    => $postData['latitude'] ?? null,
            'longitude'   => $postData['longitude'] ?? null,
            'title'       => $postData['title'] ?? null,
            'address'     => $postData['address'] ?? null,
            'tags'        => $postData['tags'] ?? null,
        ] + $pk;

        // 自定义字段
        if (isset($postData['custom_cols'])) {
            $params += $postData['custom_cols'];
        }

        return $this->call('POST', $url, $params);
    }

    /**
     * 修改数据（poi）接口（不存在则新增）
     * @param $pk
     * @param array $postData
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function touchPoi(int $geoTableId, array $pk, array $postData, int $coordType = 3)
    {
        if (! $this->detailPoi($geoTableId, $pk)) {
            return $this->createPoi($geoTableId, $postData, $coordType);
        } else {
            return $this->updatePoi($geoTableId, $pk, $postData, $coordType);
        }
    }

    /**
     * 删除数据（poi）接口
     * @param $pk
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function deletePoi(int $geoTableId, array $pk)
    {
        $url = self::URL_PREFIX . 'poi/delete';

        // 唯一主键（WHERE条件）
        $params = [
            'geotable_id' => $geoTableId,
        ] + $pk;

        return $this->call('POST', $url, $params);
    }

    /**
     * 查询指定id的数据（poi）详情接口
     * @param $pk
     * @return null
     */
    public function detailPoi(int $geoTableId, array $pk)
    {
        $url = self::URL_PREFIX . 'poi/detail';

        $params = [
            'geotable_id' => $geoTableId,
        ] + $pk;

        $result = $this->call('GET', $url, $params);

        return $result['poi'] ?? null;
    }

    /**
     * 查询列
     * @param int $geoTableId
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws Exceptions\BaiduLbsException
     */
    public function listCol(int $geoTableId)
    {
        $url = self::URL_PREFIX . 'column/list';

        $params = [
            'geotable_id' => $geoTableId
        ];

        return $this->call('GET', $url, $params);
    }

    /**
     * 创建列（create column）接口
     * @param array $postData
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function createCol(int $geoTableId, array $postData)
    {
        $url = self::URL_PREFIX . 'column/create';

        $params = [
            // 以下必填
            'geotable_id'         => $geoTableId,
            'key'                 => $postData['key'],      // 字段名（英文）
            'name'                => $postData['name'],     // 字段注释（中文）
            'type'                => $postData['type'],     // 枚举值 1:Int64, 2:double, 3:string, 4:在线图片url
            'default_value'       => $postData['default_value'] ?? null,
            'max_length'          => $postData['max_length'] ?? null,
            'is_sortfilter_field' => $postData['is_sortfilter_field'] ?? null,
            'is_search_field'     => $postData['is_search_field'] ?? null,
            'is_index_field'      => $postData['is_index_field'] ?? null,
            'is_unique_field'     => $postData['is_unique_field'] ?? null,
        ];

        return $this->call('POST', $url, $params);
    }

    /**
     * 修改指定条件列（column）接口
     * @param $colId
     * @param array $postData
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function updateCol(int $geoTableId, $colId, array $postData)
    {
        $url = self::URL_PREFIX . 'column/update';

        $params = [
            'geotable_id'         => $geoTableId,
            'id'                  => $colId,
            // 以下选填
            'name'                => $postData['name'] ?? null,
            'default_value'       => $postData['default_value'] ?? null,
            'max_length'          => $postData['max_length'] ?? null,
            'is_sortfilter_field' => $postData['is_sortfilter_field'] ?? null,
            'is_search_field'     => $postData['is_search_field'] ?? null,
            'is_index_field'      => $postData['is_index_field'] ?? null,
            'is_unique_field'     => $postData['is_unique_field'] ?? null,
        ];

        return $this->call('POST', $url, $params);
    }

    /**
     * 删除列
     * @param int $geoTableId
     * @param int $colId
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws Exceptions\BaiduLbsException
     */
    public function deleteCol(int $geoTableId, int $colId)
    {
        $url = self::URL_PREFIX . 'column/delete';

        $params = [
            'geotable_id' => $geoTableId,
            'id'          => $colId
        ];

        return $this->call('POST', $url, $params);
    }


    /**
     * 查询表
     * @param string $geoTblName
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws Exceptions\BaiduLbsException
     */
    public function listTable(string $geoTblName)
    {
        $url = self::URL_PREFIX . 'geotable/list';

        $params = [
          'name' => $geoTblName ?? ''
        ];

        return $this->call('GET', $url, $params);
    }

    /**
     * 创建表（create geotable）接口
     * @param array $postData
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function createTable(array $postData)
    {
        $url = self::URL_PREFIX . 'geotable/create';

        $params = [
            // 以下必填
            'geotype'      => $postData['geotype'] ?? 1,       // 1：点；2：线；3：面
            'name'         => $postData['name'] ?? '',         // 表名
            'is_published' => $postData['is_published'] ?? 0 , // 是否发布到检索
        ];

        return $this->call('POST', $url, $params);
    }

    /**
     * 修改表（update geotable）接口
     * @param array $postData
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function updateTable(int $geoTblId, array $postData)
    {
        $url = self::URL_PREFIX . 'geotable/update';

        $params = [
            // 以下选填
            'id'           => $geoTblId,
            'name'         => $postData['name'] ?? null,
            'is_published' => $postData['is_published'] ?? null,
        ];

        return $this->call('POST', $url, $params);
    }

    public function deleteTable(int $geoTblId)
    {
        $url = self::URL_PREFIX . 'geotable/delete';

        $params = [
            'id' => $geoTblId
        ];

        return $this->call('POST', $url, $params);
    }

    /**
     * 批量操作任务（JOB）查询进度接口
     * @param int $geoTblId
     */
    public function jobList(int $geoTblId)
    {
        $url = self::URL_PREFIX . 'job/list';

        $params = [
            'geotable_id' => $geoTblId
        ];

        return $this->call('GET', $url, $params);
    }

    /**
     * 根据id查询批量任务（detail job）接口
     * @param int $geoTblId
     * @param string $id
     */
    public function jobDetail(int $geoTblId, string $id)
    {
        $url = self::URL_PREFIX . 'job/detail';

        $params = [
            'geotable_id' => $geoTblId,
            'id'          => $id,
        ];

        return $this->call('GET', $url, $params);
    }

}
