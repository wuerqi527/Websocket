# 功能性接口

## 根据经纬度信息获取当前城市

<u class="get">GET</u> `/api/current-city`

#### 请求参数

| 字段      | 必填 | 类型   | 详细描述 |
| --------- | ---- | ------ | -------- |
| latitude  | 是   | STRING | 纬度     |
| longitude | 是   | STRING | 经度     |

#### 响应参数

| 字段      | 类型   | 详细描述 |
| --------- | ------ | -------- |
| is_default | INT | 是否默认城市 1：是 0：否 |
| city | MAP | 城市信息 |
| └area_code | INT | 城市代码 |
| └area_name | STRING | 城市名称 |
| └first_letter | STRING | 首字母 |
| └pinyin | STRING  | 拼音 |

#### 响应成功样例

```javascript
{
    "code": 0,
    "message": "当前城市",
    "data": {
        "is_default": 0,
        "city": {
            "area_code": "2113",
            "area_name": "朝阳市",
            "first_letter": "Z",
            "pinyin": "zhaoyangshi",
            "is_hot": 0
        }
    },
    "elapsed": 0.798
}
```

## 获取所有城市列表

<u class="get">GET</u> `/api/cities`

#### 请求参数

无

#### 响应参数

| 字段      | 类型   | 详细描述 |
| --------- | ------ | -------- |
| hot_cities | ARRAY | 热门城市列表 |
| cities | ARRAY | 城市列表 |

#### 响应成功样例

```javascript
{
    "code": 0,
    "message": "城市列表",
    "data": {
        "hot_cities": [
            {
                "area_code": "1101",
                "area_name": "北京市",
                "first_letter": "B",
                "pinyin": "beijingshi"
            },
            {
                "area_code": "1201",
                "area_name": "天津市",
                "first_letter": "T",
                "pinyin": "tianjinshi"
            }
        ],
        "cities": {
            "B": [
                {
                    "area_code": "1101",
                    "area_name": "北京市",
                    "first_letter": "B",
                    "spell": "beijingshi"
                },
                {
                    "area_code": "1306",
                    "area_name": "保定市",
                    "first_letter": "B",
                    "spell": "baodingshi"
                }
            ]
        }
    }
```

## 根据城市获取banner

<u class="get">GET</u> `/api/current-city`

#### 请求参数

| 字段      | 必填 | 类型   | 详细描述 |
| --------- | ---- | ------ | -------- |
| city  | 是   | INT | 尘城市code |

#### 响应参数

| 字段      | 类型   | 详细描述 |
| --------- | ------ | -------- |
| title | STRING | 标题 |
| desc | STRING | 简介 |
| img_url | STRING | 图片url |
| url | STRING | 跳转目标url |

#### 响应成功样例

```javascript
{
    "code": 0,
    "message": "广告列表",
    "data": {
        "banners": [
            {
                "id": 1,
                "city_code": 3101,
                "title": "首页banner1.0",
                "desc": "21221122qqq",
                "img_url": "https://ai-stadium-dev-1251506165.picsh.myqcloud.com/stadium/918216b8e3d2b3274fca39139e01b516.jpeg?imageView2/1/interlace/1/w/750/h/360/q/100",
                "url": "https://baidu.com",
                "display_order": 10,
                "start_time": "2018-09-07 00:00:00",
                "end_time": "2019-12-31 13:29:42",
                "status": 1,
                "created_at": "2018-08-23 21:10:12",
                "updated_at": "2018-10-18 15:08:30"
            }
        ]
    },
    "elapsed": 0.551
}
```

## 获取城市下所有区域

<u class="get">GET</u> `/api/cities/{{cityCode}}/districts`

#### 请求参数

无

#### 响应参数

| 字段      | 类型   | 详细描述 |
| --------- | ------ | -------- |
| area_code | STRING | 区域 code |
| area_name | STRING | 区域 名称 |

#### 响应成功样例

```javascript
{
    "code": 0,
    "message": "区域列表",
    "data": {
        "districts": [
            {
                "area_code": "3101",
                "area_name": "全城"
            },
            {
                "area_code": "310101",
                "area_name": "黄浦区"
            },
            {
                "area_code": "310104",
                "area_name": "徐汇区"
            },
            {
                "area_code": "310105",
                "area_name": "长宁区"
            },
            {
                "area_code": "310106",
                "area_name": "静安区"
            },
            {
                "area_code": "310107",
                "area_name": "普陀区"
            },
            {
                "area_code": "310108",
                "area_name": "闸北区"
            },
            {
                "area_code": "310109",
                "area_name": "虹口区"
            },
            {
                "area_code": "310110",
                "area_name": "杨浦区"
            },
            {
                "area_code": "310112",
                "area_name": "闵行区"
            },
            {
                "area_code": "310113",
                "area_name": "宝山区"
            },
            {
                "area_code": "310114",
                "area_name": "嘉定区"
            },
            {
                "area_code": "310115",
                "area_name": "浦东新区"
            },
            {
                "area_code": "310116",
                "area_name": "金山区"
            },
            {
                "area_code": "310117",
                "area_name": "松江区"
            },
            {
                "area_code": "310118",
                "area_name": "青浦区"
            },
            {
                "area_code": "310120",
                "area_name": "奉贤区"
            },
            {
                "area_code": "310230",
                "area_name": "崇明县"
            }
        ]
    },
    "elapsed": 0.346
}
```
