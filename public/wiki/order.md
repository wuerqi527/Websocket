# 订单API

## 订单列表

#### 请求

<u class="get">GET</u> `/api/orders`

#### 请求参数

| 字段 | 必填 | 类型 | 详细描述 |
| ---- | ---- | ---- | -------- |
| page | 否 | INT | 页码 |

#### 响应参数

|   字段    | 类型 | 详细描述  |
|--------- |-----|---------|
| orders | ARRAY [order](#!structs.md#order) | 订单列表 |

#### 响应成功样例

```javascript
{
    "code": 0,
    "message": "订单列表",
    "data": {
        "orders": [
            {
                "id": 70,
                "uid": 1,
                "order_sn": "1904011732000000289",
                "sku_id": 1,
                "failed_msg": "",
                "cancel_msg": "",
                "payment_sn": "1904011732000000119",
                "refund_sn": "",
                "total_amount": 50,
                "need_pay": 50,
                "goods_count": 1,
                "sku_info": {
                    "used_date": "2019-05-01",
                    "ticket_type": "测试票",
                    "price": "50.00",
                    "over_time": "21:00:00",
                    "begin_time": "09:00:00"
                },
                "status": 20,
                "expired_at": "2019-04-01 17:47:32",
                "created_at": "2019-04-01 17:32:32",
                "updated_at": "2019-04-01 17:32:38",
                "venue": null,
                "exihibition": null
            }
        ]
    },
    "elapsed": 0.486
}
```

## 订单详情

#### 请求

<u class="get">GET</u> `/api/orders/{{orderSn}}`

#### 响应参数

|   字段    | 类型 | 详细描述  |
|--------- |-----|---------|
| orders | MAP [order](#!structs.md#order) | 订单详情 |

#### 响应成功样例

```javascript
{
    "code": 0,
    "message": "订单详情",
    "data": {
        "order": {
            "id": 77,
            "uid": 1,
            "order_sn": "1904011752000000359",
            "sku_id": 1,
            "failed_msg": "",
            "cancel_msg": "",
            "payment_sn": "1904011752000000189",
            "refund_sn": "",
            "total_amount": 250,
            "need_pay": 250,
            "goods_count": 5,
            "sku_info": {
                "name": "测试票",
                "price": "50.00",
                "over_time": "21:00:00",
                "used_date": "2019-05-01",
                "begin_time": "09:00:00"
            },
            "status": 20,
            "expired_at": "2019-04-01 18:07:27",
            "created_at": "2019-04-01 17:52:27",
            "updated_at": "2019-04-01 17:52:34",
            "venue": null,
            "exihibition": null,
            "sku": {
                "id": 1,
                "exihibition_id": 500,
                "used_date": "2019-05-01",
                "ticket_type": "测试票",
                "begin_time": "09:00:00",
                "over_time": "21:00:00",
                "price": "50.00",
                "stock": 66,
                "limit": 5,
                "deleted_at": null,
                "created_at": null,
                "updated_at": "2019-04-01 17:52:27"
            },
            "tickets": [
                {
                    "id": 170,
                    "uid": 1,
                    "ticket_no": "190401175200259872581",
                    "order_sn": "1904011752000000359",
                    "price": "50.00",
                    "used_date": "2019-05-01",
                    "begin_time": "09:00:00",
                    "over_time": "21:00:00",
                    "used_at": null,
                    "qr_code": "https://ai-gmall-local-1251506165.cos.ap-shanghai.myqcloud.com/tickets/SGFvDtfr0AkFgxlDA9jYNPGlkMojuubRRQg27mpL",
                    "status": 0,
                    "created_at": null,
                    "updated_at": null
                }
            ]
        }
    },
    "elapsed": 0.554
}
```

## 创建订单

#### 请求

<u class="post">POST</u> `/api/orders`

#### 请求参数

##### 创建场票预订单

| 字段 | 必填 | 类型 | 详细描述 |
| ---- | ---- | ---- | -------- |
| sku_id | 是 | INT | sku id |
| goods_count | 是 | INT | 购买数量 |


#### 响应参数

|   字段    | 类型 | 详细描述  |
|--------- |-----|---------|
| orders | MAP [order](#!structs.md#order) | 订单详情 |

#### 响应成功案例

```javascript
{
    "code": 0,
    "message": "create",
    "data": {
        "order": {
            "uid": 1,
            "order_sn": "1904011752000000359",
            "sku_id": 1,
            "total_amount": 250,
            "need_pay": 250,
            "goods_count": "5",
            "status": 0,
            "expired_at": "2019-04-01T10:07:27.325788Z",
            "sku_info": {
                "used_date": "2019-05-01",
                "name": "测试票",
                "price": "50.00",
                "begin_time": "09:00:00",
                "over_time": "21:00:00"
            },
            "updated_at": "2019-04-01 17:52:27",
            "created_at": "2019-04-01 17:52:27",
            "sku": {
                "id": 1,
                "exihibition_id": 500,
                "used_date": "2019-05-01",
                "begin_time": "09:00:00",
                "over_time": "21:00:00",
                "name": "测试票",
                "price": "50.00",
                "stock": 66,
                "limit": 5,
                "deleted_at": null,
                "created_at": null,
                "updated_at": "2019-04-01 17:49:12"
            }
        }
    },
    "elapsed": 0.617
}
```
