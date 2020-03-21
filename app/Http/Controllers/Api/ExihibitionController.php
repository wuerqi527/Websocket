<?php

namespace App\Http\Controllers\Api;

use App\Models\Exihibition;
use Illuminate\Http\Request;
use App\Business\ExihibitionBusiness;

class ExihibitionController extends AbstractController
{
    // 展会列表
    public function exihibitions(Request $request)
    {
        $posts = $request->validate([
            'keyword'     => 'nullable|string',
            'page'        => 'nullable|integer|min:1',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'coord_type'  => 'nullable|integer',
            'sort'        => 'nullable|in:' . implode(',', array_keys(Exihibition::SORT_TYPES)),
            'area_code'   => 'nullable|exists:areas,area_code',
        ]);

        $params = [
            $posts['keyword'] ?? '',
            $posts['area_code'] ?? '',
            $posts['sort'] ?? Exihibition::SORT_DISTANCE,
            $posts['coord_type'] ?? 0,
            $posts['latitude'] ?? 0,
            $posts['longitude'] ?? 0,
            $posts['page'] ?? 1,
        ];

       try{
            $exihibitions = ExihibitionBusiness::getExihibitionsByLbs(...$params);
       }
       catch (\Throwable $e) {
           $exihibitions = ExihibitionBusiness::getExihibitionsByLocal(...$params);
       }

        return ok('展会列表', compact('exihibitions'));
    }

    // 展会详情
    public function detail(Request $request, Exihibition $exihibition)
    {
        $exihibition->append('skus');

        return ok('展会详情', compact('exihibition'));
    }

    // 根据关键词自动联想展会名
    public function names(Request $request)
    {
        $posts = $request->validate([
            'keyword'   => 'nullable|string|max:200',
            'city_code' => 'nullable|exists:areas,area_code',
        ]);

        $names = ExihibitionBusiness::getExihibitionNamesByKeyword($posts['keyword'] ?? '', $posts['city_code'] ?? '');

        return ok('展会名列表', compact('names'));
    }
}
