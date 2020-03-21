<?php

namespace App\Http\Controllers\Api;

use App\Models\Area;
use App\Services\CosSts;
use Illuminate\Http\Request;
use App\Business\UtilBusiness;

class UtilController extends AbstractController
{
    // 获取 COS 直传凭证
    // @see https://github.com/tencentyun/cos-wx-sdk-v5
    public function getCosUploadToken()
    {
        $sts = new CosSts(config('cloud.cos_sts'));

        $upToken = $sts->getCosUploadToken('ugc/*');

        return ok('COS 直传凭证', $upToken);
    }

    // 首页广告
    public function banners(Request $request)
    {
        $request->validate([
            'city_code' => 'required'
        ]);

        $banners = UtilBusiness::getBanners($request->city_code);

        return ok('广告列表', compact('banners'));
    }

    // 根据经纬度获取城市信息
    public function getCityByCoordinate(Request $request)
    {
        $latitude   = $request->latitude ?? 0;
        $longitude  = $request->longitude ?? 0;

        $city = UtilBusiness::getCityByCoordinate($longitude, $latitude);

        return ok('当前城市', compact('city'));
    }

    // 获取所有城市信息
    public function getCities()
    {
        $cities = UtilBusiness::getCities();

        return ok('城市列表', $cities);
    }

    // 根据 city code 获取城市下所有区域
    public function getDistricts(Request $request, Area $city)
    {
        if ($city->area_level != Area::LEVEL_CITY) {
            throws('Invalid City Code');
        }

        $city->children->prepend([
            'area_code' => $city->area_code,
            'area_name' => '全城',
        ]);

        $districts = $city->children->toArray();

        return ok('区域列表', compact('districts'));
    }
}
