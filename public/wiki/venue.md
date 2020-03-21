# 展馆API

## 展馆列表

#### 请求

GET</u> `/api/venues`

#### 请求参数

| 字段 | 必填 | 类型 | 详细描述 |
| ---- | ---- | ---- | -------- |
| page | 否 | INT | 页码 |
| sort | 否 | INT |  (1: 距离排序 2：展会数量排序) |
| latitude | 否 | FLOAT | 纬度值 (31.176283204021) |
| longitude | 否 | FLOAT | 经度值（121.42254280659）|
| keyword | 否 | STRING | 关键词 |
| coord_type | 否 | INT | 坐标系类型：</br>1：GPS设备获取的角度坐标，wgs84坐标;</br>2：GPS获取的米制坐标、sogou地图所用坐标;</br>3：火星坐标系;</br>4：3中列表地图坐标对应的米制坐标;</br>5：百度地图采用的经纬度坐标;</br>6：百度地图采用的米制坐标;</br>7：mapbar地图坐标;</br>8：51地图坐标 |
| area_code | 否 | STRING | 区域编码 |

#### 响应参数

|   字段    | 类型 | 详细描述  |
|--------- |-----|---------|
| venues | ARRAY [venue](#!structs.md#venue) | 展馆列表 |

#### 响应成功样例

```javascript
{
    "code": 0,
    "message": "展馆列表",
    "data": {
        "venues": [
            {
                "id": 1,
                "name": "you",
                "cover_url": "https://ai-stadium-dev-1251506165.cos.ap-shanghai.myqcloud.com/stadium/2018080616/BP8RW1rgE3HjdkIakBJ8hWD5ve4qvazEZVmRl3wV",
                "latitude": "31.176283204021",
                "longitude": "121.42254280658724",
                "province_code": "3100",
                "city_code": "3101",
                "district_code": "310104",
                "address": "上海市徐汇区田林路",
                "keywords": "you you 篮球 游泳 上海市",
                "phone": "[\"\"]",
                "status": 1,
                "poi_id": 17,
                "updated_at": "2018-10-31 14:37:47",
                "created_at": null,
                "deleted_at": null,
                "distance": 0,
                "exihibition_count": 1,
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
                        "deleted_at": null
                    }
                ]
            }
        ]
    },
    "elapsed": 0.371
}
```

## 展馆详情

#### 请求

<u class="get">GET</u> `/api/venues/{{venueId}}`

#### 请求参数

无

#### 响应参数

|   字段    | 类型 | 详细描述  |
|--------- |-----|---------|
| venue | MAP [venue](#!structs.md#venue) | 展馆详情 |

#### 响应示例

```javascript
{
    "code": 0,
    "message": "展馆详情",
    "data": {
        "venue": {
            "id": 1,
            "name": "you",
            "cover_url": "https://ai-stadium-dev-1251506165.cos.ap-shanghai.myqcloud.com/stadium/2018080616/BP8RW1rgE3HjdkIakBJ8hWD5ve4qvazEZVmRl3wV",
            "latitude": "31.176283204021",
            "longitude": "121.42254280658724",
            "province_code": "3100",
            "city_code": "3101",
            "district_code": "310104",
            "address": "上海市徐汇区田林路",
            "keywords": "you you 篮球 游泳 上海市",
            "phone": "[\"\"]",
            "status": 1,
            "poi_id": 17,
            "updated_at": "2018-10-31 14:37:47",
            "created_at": null,
            "deleted_at": null,
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
                    "deleted_at": null
                }
            ]
        }
    },
    "elapsed": 0.623
}
```

## 按关键词检索展馆名列表

#### 请求

<u class="get">GET</u> `/api/venue-names`

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
    "message": "展馆名列表",
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
