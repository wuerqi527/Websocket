
# 用户API

## 获取用户信息

<u class="get">GET</u> `/api/user`

#### 请求参数

无

#### 响应参数

| 字段    | 类型   | 详细描述         |
| ------- | ------ | ------------ |
| user | ARRAY | 用户信息 |
| └id   | INT    | 用户id    |
| └api_token   | STRING    | 用户token    |
| └nickname | STRING | 用户名 |
| └mobile | STRING | 用户手机号 |
| └avatar_url | STRING | 头像地址 |
| └gender | INT | 性别 1：那么 2：女 |
| └realname | STRING | 真实姓名 |
| └address | STRING | 地址 |
| └country | STRING | 国家 |
| └province | STRING | 省份 |
| └city | STRING | 城市 |
| └unpaid_order | STRING | 未支付订单号 |

#### 响应成功样例

```javascript
{
    "code": 0,
    "message": "user",
    "data": {
        "user": {
            "id": 1,
            "mina_openid": "oaSvz0ErQkG2Xh4UJ4GdSGQ3Mv1o",
            "wx_unionid": "",
            "nickname": "陈问渔",
            "avatar_url": "",
            "gender": 1,
            "realname": "",
            "mobile": "15821133132",
            "address": "",
            "country": "",
            "province": "",
            "city": "",
            "created_at": null,
            "updated_at": null,
            "stats": {
                "unpaid_order_count": 1,
                "successed_order_count": 1,
                "unused_coupon_count": 3,
                "used_coupon_count": 1,
                "expired_coupon_count": 0
            }
        }
    },
    "elapsed": 2.967
}
```

## 获取用户微信绑定手机号并绑定至我方用户账号

<u class="post">POST</u> `/api/user/wechat-mobile`

#### 请求参数

| 字段 | 必填 | 类型 | 详细描述 |
| ---- | ---- | ---- | -------- |
| encrypted_data | 是 |  STRING | 加密串 |
| iv | 是 |  STRING | 初始向量 |

#### 响应参数

| 字段    | 类型     | 详细描述         |
| ---------- | --- | ------------ |
| mobile | STRING | 解析成功的手机号 |

#### 响应成功样例

```javascript
{
    "code": 0,
    "message": "weChatMobile",
    "data": {
        "mobile": "150**47153"
    },
    "elapsed": 2.803
}
```

## 保存 form_id

#### 初始化

<u class="get">POST</u> `/api/user/save-form-id`

#### 请求参数

| 字段    | 必填 | 类型   | 详细描述                                  |
| ------- | ---- | ------ | ----------------------------------------- |
| form_id | 是   | STRING | 表单提交后返回的form_id，用于触发模板消息 |

#### 响应参数

暂无

#### 响应成功样例

```javascript
{
    "code": 0,
    "message": "saveFormId",
    "data": null,
    "elapsed": 0.202
}
```
