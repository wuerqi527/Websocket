# 数据结构

## venue

### 展馆信息

| 字段    | 类型     | 详细描述         |
| -------| ------ | ------------ |
| id      | INT | 展馆id  |
| name    | STRING | 展馆名 |
| cover_url | STRING | 封面图 |
| latitude | STRING | 纬度 |
| longitude | STRING | 经度 |
| province_code | STRING | 省code |
| city_code | STRING | 市code |

| district_code | STRING | 区code |
| address  | STRING | 展馆地址 |
| phone  | STRING | 联系方式 |
| distance | FLOAT | 距离(单位:m) |
| exihibition_count | INT | 展会数量 |
| exihibitions | MAP [exihibition](#!structs.md#exihibition) | 展会列表 |

## exihibition

### 展会信息

| 字段    | 类型     | 详细描述         |
| -------| ------ | ------------ |
| id      | INT | 展会id  |
| name    | STRING | 展会名 |
| cover_url | STRING | 封面图 |
| latitude | STRING | 纬度 |
| longitude | STRING | 经度 |
| province_code | STRING | 省code |
| city_code | STRING | 市code |
| district_code | STRING | 区code |
| address  | STRING | 展会地址 |
| phone  | STRING | 联系方式 |
| distance | FLOAT | 距离(单位:m) |
| exihibition_count | INT | 展会数量 |
| exihibitions | ARRAY | 展会列表 |
| └id | INT | 展会id |
| └venue_id | INT | 所属展会id |
| └name | STRING | 展会名称 |
| └cover_url | STRING | 展会封面 |
| └detail | STRING | 展会详情 富文本 |
| └latitude | STRING | 展会纬度 |
| └longitude | STRING | 展会经度 |
| └province_code | STRING | 省code |
| └city_code | STRING | 市code |
| └district_code | STRING | 区code |
| └address | STRING | 展会地址 |
| └first_play_date | STRING | 第一场日期 |
| └last_play_date | STRING | 最后一场日期 |
| └start_sale_at | STRING | 开售日期 |
| └end_sale_at | STRING | 结束售卖日期 |
| └start_time | STRING | 展会开始时间 |
| └end_time | STRING | 展会结束时间 |
| └min_price | STRING | SKU最低价格 单位（元） |
| └max_price | STRING | SKU最高价格 单位（元） |
| └phone | STRING | 联系电话 |
| └└skus | ARRAY | 展会下sku列表 |
| └└date | STRING | 售卖日期 |
| └└week | STRING | 售卖星期 |
| └└skus | ARRAY [sku](#!structs.md#sku) | 该售卖日期下的sku列表 |

## order

### 订单信息

| 字段    | 类型     | 详细描述         |
| -------| ------ | ------------ |
| id      | INT | 订单id  |
| uid    | INT | 订单创建人uid |
| order_sn | STRING | 订单流水号 |
| sku_id | INT | 订单sku id |
| cancel_msg | STRING | 取消原因 |
| payment_sn | STRING | 支付单号 |
| total_amount | FLOAT | 总金额 （单位：元） |
| need_pay | STRING | 支付金额 （单位：元） |
| goods_count  | INT | 商品数量 |
| sku_info  | MAP [sku](#!structs.md#sku) | SKU 信息 |
| status  | INT | 订单状态 0：待支付 10:已支付 20：已出票 97：已完成 99：已取消 |
| expired_at  | STRING | 过期时间 |
| tickets  | ARRAY [ticket](#!structs.md#ticket) | 票列表 |
| venue  | MAP [venue](#!structs.md#venue) | 展馆 |
| exihibition  | MAP [exihibition](#!structs.md#exihibition) | 展会 |

## sku

### sku信息

| 字段    | 类型     | 详细描述         |
| -------| ------ | ------------ |
| id | INT | sku id |
| ticket_type | STRING | sku 票类型 |
| price | INT | 价格 单位（元） |
| stock | INT | 库存 |
| limit | INT | 限购数量 |
| used_date | INT | 票使用日期 |
| begin_time | INT | 开场时间 |
| over_time | INT | 结束时间 |

## ticket

### 票信息

| 字段    | 类型     | 详细描述         |
| -------| ------ | ------------ |
| ticket_no | STRING | 票号 |
| price | FLOAT | 票价格 单位（元） |
| used_date | STRING | 票使用日期 |
| qr_code | STRING | 票二维码 |
| status | INT | 票状态 0：未使用 10：已使用 20：已过期|
