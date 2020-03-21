# 这里写模块名称

## 这里写接口名称

#### 请求

<u class="post">POST</u> `/login/index`

#### 请求参数

| 字段 | 必填 | 类型 | 详细描述 |
| ---- | ---- | ---- | -------- |
| code | 是 | STRING | 微信小程序授权码 |

#### 响应参数

| 字段 | 类型 | 详细描述 |
| ---- | ---- | -------- |
| token | STRING | 登录凭证 |

#### 响应成功样例

```javascript
{
    "code": 0,
    "msg": "success",
    "data": {
        "token": "bb6e6721562d0ad6468d4c57a4fb19cae04541e2"
    }
}
```
