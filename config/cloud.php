<?php

return [

    // 腾讯云 API 密钥
    'qcloud' => [
        'app_id'     => env('QCLOUD_APP_ID'),
        'secret_id'  => env('QCLOUD_SECRET_ID'),
        'secret_key' => env('QCLOUD_SECRET_KEY'),
    ],

    // 腾讯云cos直传
    'cos_sts' => [
        'url'        => 'https://sts.api.qcloud.com/v2/index.php',
        'domain'     => 'sts.api.qcloud.com',
        'app_id'     => env('COSV5_APP_ID'),
        'secret_id'  => env('COSV5_SECRET_ID'),
        'secret_key' => env('COSV5_SECRET_KEY'),
        'bucket'     => env('COSV5_BUCKET'),
        'region'     => env('COSV5_REGION_AP'),
    ],

    // 百度LBS
    'baidu_lbs' => [
        'app_key'    => env('BAIDU_LBS_AK'),
        'secret_key' => env('BAIDU_LBS_SK'),
        'tables' => [
            'venues' => [
                'tbl_name' => env('BAIDU_LBS_TBL_VENUE_NAME'),
                'tbl_id'   => env('BAIDU_LBS_TBL_VENUE_ID'),
                'pk'       => env('BAIDU_LBS_TBL_VENUE_PK'),
            ],
            'exihibitions' => [
                'tbl_name' => env('BAIDU_LBS_TBL_EXIHIBITION_NAME'),
                'tbl_id'   => env('BAIDU_LBS_TBL_EXIHIBITION_ID'),
                'pk'       => env('BAIDU_LBS_TBL_EXIHIBITION_PK'),
            ],
        ]
    ],
];
