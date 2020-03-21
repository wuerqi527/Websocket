# Stadium Api 说明文档

## Api 网关地址

开发环境：https://dev.api.exihibition.wetax.com.cn

测试环境：https://qa.api.exihibition.wetax.com.cn

生产环境：https://api.exihibition.wetax.com.cn

## 全局 HTTP 请求头

`请把以下参数放入 HTTP 请求头中哟~`

| 名字 | 必填 | 类型 | 详细描述 |
| ---- | ---- | ---- | :------- |
| X-APP-ID | 是 | INT | 应用ID <br />Web: 1<br /> Android: 2<br /> iOS: 3 |
| X-APP-VER | 否 | STRING | 客户端版本号，例如 1.2.3 （约定采用三段版本号） |
| Authorization | 是 | STRING | 登录凭证（登录、注册接口返回的一个 token）注意格式：Bearer {token} |
| Content-Type | 是 | STRING | 请求内容类型：<br />application/x-www-form-urlencoded <br />application/json |

## 全局响应格式

| 名字 | 类型 | 详细描述 |
| ---- | ---- | -------- |
| code | INT | 响应代码 |
| message | STRING | 响应文本 |
| data | MAP/NULL | 响应内容 |

## 全局响应代码

| code | 详细描述 |
| --------- | -------- |
| 0 | 成功 |
| -1 | 普通异常，详见 `message` 字段描述<br />客户端可以 `toast` 形式显示异常文本 |
| -1000 | 无效的登录凭证，请前往登录 |

