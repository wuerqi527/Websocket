<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Api'], function () {

    // 登录
    Route::post('/auth/login', 'AuthController@login');

    // 热搜词列表
    Route::get('/hot-keywords', 'UtilController@hotKeywords');

    // 根据经纬度获取所在城市
    Route::get('/current-city', 'UtilController@getCityByCoordinate');

    // 获取所有城市信息
    Route::get('/cities', 'UtilController@getCities');

    Route::get('/cities/{city}/districts', 'UtilController@getDistricts');

    // 首页banner
    Route::get('/banners', 'UtilController@banners');

    // 关键词搜索展馆名列表
    Route::get('/venue-names', 'VenueController@names');

    // 首页展馆列表
    Route::get('/venues', 'VenueController@venues');

    // 展馆详情
    Route::get('/venues/{venue}', 'VenueController@detail');

    // 首页展会列表
    Route::get('/exihibitions', 'ExihibitionController@exihibitions');

    // 展会详情
    Route::get('/exihibitions/{exihibition}', 'ExihibitionController@detail');

    // 关键词搜索展馆名列表
    Route::get('/exihibition-names', 'ExihibitionController@names');

    Route::group([

        // 必须登录后访问
        'middleware' => 'auth:api',

    ], function () {

        // 获取 COS 直传凭证
        Route::get('/up-token', 'UtilController@getCosUploadToken');

        // 获取用户信息
        Route::get('/user', 'UserController@user');

        // 获取用户微信绑定手机号并绑定至我方用户账号
        Route::post('/user/wechat-mobile', 'UserController@bindWxMobile');

        // 保存form_id
        Route::post('/user/save-form-id', 'UserController@saveFormId');

        // 正式创建订单
        Route::post('/orders', 'OrderController@create');

        // 发起支付
        Route::post('/orders/{order}/payment', 'OrderController@payment');

        // 我的订单
        Route::get('/orders', 'OrderController@myOrders');

        // 订单详情
        Route::get('/orders/{order}', 'OrderController@detail');

        // 取消订单
        Route::post('/orders/{order}/cancel', 'OrderController@cancel');
    });

});
