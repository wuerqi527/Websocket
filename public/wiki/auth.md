# 用户认证

## 用户登录

#### 请求

<u class="post">POST</u> `/api/auth/login`

#### 请求参数
| 字段 | 类型 | 必填 | 详细描述 |
| -------- | ------ | ------ |-------- |
| code | STRING | 是 | 用户小程序code |
| raw_data | STRING | 是 |不包括敏感信息的原始数据字符串，用于计算签名。 |
| signature | STRING | 是 | 使用 sha1( rawData + sessionkey ) 得到字符串，用于校验用户信息，参考文档 signature。 |
| encrypted_data | STRING | 是 | 包括敏感数据在内的完整用户信息的加密数据 |
| iv | STRING | 是 | 加密算法的初始向量 |

#### 响应参数

| 字段  | 类型   | 详细描述            |
| :---- | ------ | ------------------- |
| token | STRING | Bearer 登录凭证 |

#### 响应成功样例

```javascript
{
    "code": 0,
    "message": "登录成功",
    "data": {
        "token": "6c17f26e577b45004436a846950a87edf4cbb298"
    }
}
```

## 退出登录

#### 请求

<u class="post">POST</u> `/api/auth/logout`

#### 响应成功样例

```javascript
{
    "code": 0,
    "message": "登出成功",
    "data": null
}
```