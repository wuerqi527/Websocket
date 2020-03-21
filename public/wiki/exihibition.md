# 展会API

## 展会列表

#### 请求

GET</u> `/api/exihibitions`

#### 请求参数

| 字段 | 必填 | 类型 | 详细描述 |
| ---- | ---- | ---- | -------- |
| page | 否 | INT | 页码 |
| sort | 否 | INT |  (1: 距离排序 2：价格排序) |
| latitude | 否 | FLOAT | 纬度值 (31.176283204021) |
| longitude | 否 | FLOAT | 经度值（121.42254280659）|
| keyword | 否 | STRING | 关键词 |
| coord_type | 否 | INT | 坐标系类型：</br>1：GPS设备获取的角度坐标，wgs84坐标;</br>2：GPS获取的米制坐标、sogou地图所用坐标;</br>3：火星坐标系;</br>4：3中列表地图坐标对应的米制坐标;</br>5：百度地图采用的经纬度坐标;</br>6：百度地图采用的米制坐标;</br>7：mapbar地图坐标;</br>8：51地图坐标 |
| area_code | 否 | STRING | 区域编码 |

#### 响应参数

|   字段    | 类型 | 详细描述  |
|--------- |-----|---------|
| exihibitions | ARRAY [exihibition](#!structs.md#exihibition) | 展会列表 |

#### 响应成功样例

```javascript
{
    "code": 0,
    "message": "展会列表",
    "data": {
        "exihibitions": [
            {
                "id": 500,
                "venue_id": 1,
                "name": "测试展会",
                "cover_url": "https://ai-stadium-dev-1251506165.cos.ap-shanghai.myqcloud.com/stadium/2018080616/BP8RW1rgE3HjdkIakBJ8hWD5ve4qvazEZVmRl3wV",
                "detail": null,
                "latitude": "31.176283204021",
                "longitude": "121.42254280658724",
                "province_code": "",
                "city_code": "3101",
                "district_code": "310104",
                "address": "上海市闸北区和田路295号",
                "first_play_date": "2019-05-01",
                "last_play_date": "2019-05-05",
                "start_sale_at": "2019-03-01 00:00:00",
                "end_sale_at": "2019-05-05 00:00:00",
                "start_time": "09:00:00",
                "end_time": "21:00:00",
                "min_price": "50.00",
                "max_price": "200.00",
                "keywords": "",
                "phone": null,
                "INTroduce": null,
                "status": 1,
                "poi_id": null,
                "updated_at": null,
                "created_at": null,
                "deleted_at": null,
                "distance": 0
            }
        ]
    },
    "elapsed": 0.456
}
```

## 展会详情

#### 请求

<u class="get">GET</u> `/api/exihibitions/{{exihibitionId}}`

#### 请求参数

无

#### 响应参数

|   字段    | 类型 | 详细描述  |
|--------- |-----|---------|
| exihibition | MAP [exihibition](#!structs.md#exihibition) | 展会详情 |

#### 响应示例

```javascript
{
    "code": 0,
    "message": "展会详情",
    "data": {
        "exihibition": {
            "id": 500,
            "venue_id": 1,
            "name": "测试展会",
            "cover_url": "https://ai-stadium-dev-1251506165.cos.ap-shanghai.myqcloud.com/stadium/2018080616/BP8RW1rgE3HjdkIakBJ8hWD5ve4qvazEZVmRl3wV",
            "detail": null,
            "latitude": "31.176283204021",
            "longitude": "121.42254280658724",
            "province_code": "",
            "city_code": "3101",
            "district_code": "310104",
            "address": "上海市闸北区和田路295号",
            "first_play_date": "2019-05-01",
            "last_play_date": "2019-05-05",
            "start_sale_at": "2019-03-01 00:00:00",
            "end_sale_at": "2019-05-05 00:00:00",
            "start_time": "09:00:00",
            "end_time": "21:00:00",
            "min_price": "50.00",
            "max_price": "200.00",
            "keywords": "",
            "phone": null,
            "status": 1,
            "poi_id": null,
            "updated_at": null,
            "created_at": null,
            "deleted_at": null,
            "skus": [
                {
                    "date": "2019-05-01",
                    "week": "周三",
                    "skus": [
                        {
                            "id": 1,
                            "exihibition_id": 500,
                            "used_date": "2019-05-01",
                            "begin_time": "09:00:00",
                            "over_time": "21:00:00",
                            "ticket_type": "测试票",
                            "price": "50.00",
                            "stock": 66,
                            "limit": 5,
                            "deleted_at": null,
                            "created_at": null,
                            "updated_at": "2019-04-01 17:52:27"
                        }
                    ]
                }
            ]
        }
    },
    "elapsed": 0.514
}
```

## 按关键词检索展会名列表

#### 请求

<u class="get">GET</u> `/api/exihibition-names`

#### 请求参数
| 字段 | 必填 | 类型 | 详细描述 |
| ---- | ---- | ---- | -------- |
| keyword | 否 | STRING | 关键词 |
| city_code | 否 | STRING | 城市code |

#### 响应参数
无

#### 响应示例
```javascript
{
    "code": 0,
    "message": "展会名列表",
    "data": {
        "names": [
            "嘉定区南翔体育馆",
            "球王运动体育馆测试",
            "球王运动体育馆",
            "体育场",
            "体育馆",
            "体育宾馆",
            "赣东大堤风光体育中心"
        ]
    },
    "elapsed": 5.916
}
```
