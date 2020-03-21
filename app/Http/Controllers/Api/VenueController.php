<?php

namespace App\Http\Controllers\Api;

use App\Models\Venue;
use Illuminate\Http\Request;
use App\Business\VenueBusiness;

class VenueController extends AbstractController
{
    // 展馆列表
    public function venues(Request $request)
    {
        $posts = $request->validate([
            'keyword'     => 'nullable|string',
            'page'        => 'nullable|integer|min:1',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'coord_type'  => 'nullable|integer',
            'sort'        => 'nullable|in:' . implode(',', array_keys(Venue::SORT_TYPES)),
            'area_code'   => 'nullable|exists:areas,area_code',
        ]);

        $params = [
            $posts['keyword'] ?? '',
            $posts['area_code'] ?? '',
            $posts['sort'] ?? Venue::SORT_DISTANCE,
            $posts['coord_type'] ?? 0,
            $posts['latitude'] ?? 0,
            $posts['longitude'] ?? 0,
            $posts['page'] ?? 1,
        ];

       try{
            $venues = VenueBusiness::getVenuesByLbs(...$params);
       }
       catch (\Throwable $e) {
           $venues = VenueBusiness::getVenuesByLocal(...$params);
       }

        return ok('展馆列表', compact('venues'));
    }

    // 展馆详情
    public function detail(Request $request, Venue $venue)
    {
        // 加载展会列表
        $venue->load('exihibitions');

        return ok('展馆详情', compact('venue'));
    }

    // 根据关键词自动联想展馆名
    public function names(Request $request)
    {
        $posts = $request->validate([
            'keyword'   => 'nullable|string|max:200',
            'city_code' => 'nullable|exists:areas,area_code',
        ]);

        $names = VenueBusiness::getVenueNamesByKeyword($posts['keyword'] ?? '', $posts['city_code'] ?? '');

        return ok('展馆名列表', compact('names'));
    }
}
