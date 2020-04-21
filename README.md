## HitPoints API PHP SDK

该SDK封装了从Web服务端调用HitPoints API的方法


## 安装

在composer.json中添加

```json
"repositories": {
    "seagm-libs/hitpoints": {
      "type": "vcs",
      "url": "git@github.com:seagm/hitpoints-php-sdk.git"
    }
  }
```

或者执行下面的命令

```
composer config repositories.seagm-libs/hitpoints vcs git@github.com:seagm/hitpoints-php-sdk.git
```

然后执行命令

```text
composer require seagm-libs/hitpoints
```


## 使用

### 配置参数

```PHP
// 配置参数，根据不同环境修改为相应的参数
$options = [
    'app_id' => 'gIoyQaaZ1zICMtJoXYOrw22o',
    'app_secret' => '8zNab91jd77V7cPx5hq62jh9F_kz4GK8NeoLszLTCkGg',
    'api_base' => 'http://open-api.hitpoints.test',
    'rsa_private_key_file'=>'',
    'rsa_server_key_file' =>''
];
```


配置参数说明:

| 参数名称   | 参数说明                        | 示例值                      |
| ---------- | ------------------------------- | --------------------------- |
| App_id | HitPoints开发者ID      |  |
| App_secret | HitPoints开发者密钥 |                             |
| Api_base | HitPoints API URL | https://open-api.hitpoints.com |
| rsa_private_key_file | Rsa私钥证书文件路径 | /var/cert/rsa_private_key.pem |
| rsa_server_key_file | Rsa HitPoints API公钥 | /var/cert/rsa_hitpoints_key.pem |

**备注**

如果开发者账号没有启用rsa非对称验签，请不要填写rsa_private_key_file参数；如果填写了该参数，则默认开启rsa证书验签，如果服务端没有配置相应的公钥证书，则会导致验签失败。

当使用RSA证书后，SDK对请求参数和响应参数的签名与验签都将采用Rsa证书。使用Rsa证书，需要先在服务端开启，不可单方面在SDK中启用。

如何开启Rsa证书安全？

---

### 获取已开通的产品

```php
use SeaGM\HitPoints\HitPoints;
use SeaGM\HitPoints\Exception\ApiClientException;
use SeaGM\HitPoints\Exception\ApiException;

// query opened HitPoints items for this account
try {
    $api = HitPoints::apiClient($options);
    $items = $api->newItemsRequest();
    // if the account has opened multi currency items, you can set currency filter to get specified currency items
    //$items->setCurrency('CNY');

    $result = $api->request($items);

    print_r($result);
    // $result['data']

} catch (ApiClientException $e) {
    var_dump($e->getMessage());
} catch (ApiException $e) {
    var_dump($e->getMessage());
}
```

success $result

```php
Array
(
    [data] => Array
        (
            [pin-value-fixed] => Array
                (
                    [0] => Array
                        (
                            [product_id] => 4
                            [product_name] => Happy Card
                            [category_name] => 20 Yuan
                            [category_code] => happy-card-a
                            [region] => BC
                            [currency] => CNY
                            [par_value] => 20.00
                            [discount_rate] => 0.00%
                            [max_fetch_quantity] => 5
                        )

                    [1] => Array
                        (
                            [product_id] => 2
                            [product_name] => Happy Card
                            [category_name] => 10 Yuan
                            [category_code] => happy-card-a
                            [region] => BC
                            [currency] => CNY
                            [par_value] => 10.00
                            [discount_rate] => 0.00%
                            [max_fetch_quantity] => 10
                        )

                )

        )

    [code] => 200
    [message] => ok
    [success] => true
)
```

**接口说明**

- $result['data'] 是一个关联数组, 数据结构如下

  ```PHP
  $result['data'] = [
  		'item-category-key-1'=>[], // category-1 product list
  		'item-category-key-2'=>[], // category-2 product list
  ]
  ```

----

### 创建支付token

```php
use SeaGM\HitPoints\HitPoints;
use SeaGM\HitPoints\Exception\ApiClientException;
use SeaGM\HitPoints\Exception\ApiException;

/ Merchant has created trade order and call HitPoints pay.
$merchantOutTradeId = date('YmdHis');
$paySuccessPageUrl = 'https://merchant.example.com/order/success?transaction=' . $merchantOutTradeId;

try {
    $api = HitPoints::apiClient($options);
    $payToken = $api->newPayTokenRequest();
    // set request parameters
    $payToken->setReferenceId($merchantOutTradeId); // required
    $payToken->setRedirectUrl($paySuccessPageUrl); // optional
    $payToken->setCurrency('CNY'); // optional
    $payToken->setAmount('10.00'); // optional
    $payToken->setBody('Game direct top up'); // optional
    $payToken->setDetail('Game top up with hitpoints currency MYR and amount=10.'); // optional
    $payToken->setAttach('order=12&transaction=1&user_name=bbc'); // optional

    $result = $api->request($payToken);

    var_dump($result);
    // $result['data']

} catch (ApiClientException $e) {
    var_dump($e->getMessage());
} catch (ApiException $e) {
    var_dump($e->getMessage());
}
```

$result

```PHP
Array
(
    [data] => Array
        (
            [token] => z60lRM3cuxC2oVIA66nfo839JiYPrHH1saIq7JA4CA==
            [reference_id] => 20200420095816
            [currency] => CNY
            [amount] => 10.00
            [body] => Game direct top up
            [detail] => Game top up with hitpoints currency MYR and amount=10.
            [attach] => order=12&transaction=1&user_name=bbc
            [state] => pending
            [redirect_url] => https://merchant.example.com/order/success?transaction=20200420095816
            [pay_method] => web
            [expire_time] => 1587378497
        )

    [code] => 200
    [message] => ok
    [success] => true
)
```

**接口说明**

该接口为运营商提供创建支付token。

使用HitPoints支付的流程为，运营商通过API创建支付token，获得token后将token作为url参数，引导用户跳转到HitPoints的支付验证页面。

用户在HitPoints的支付验证页面输入HitPoints PIN并确认支付后。HitPoints将异步通知(callback)运营商用户支付结果，同时如果运营设置了跳转网页(redirect_url)，HitPoints将在用户完成支付后，引导用户跳转到运营商指定的网址。

HitPoints的支付网址是？

---

### 查询支付结果

```PHP
use SeaGM\HitPoints\HitPoints;
use SeaGM\HitPoints\Exception\ApiClientException;
use SeaGM\HitPoints\Exception\ApiException;

// Query pay result
try {
    $api = HitPoints::apiClient($options);
    $queryReload = $api->newQueryReloadRequest();
    // from below three parameters, choose one for your situation
    $queryReload->setReferenceId('20200420102238'); // Merchant reference id
    // $queryReload->setTransactionId('hitosladskfka');// HitPoints transaction id
    // $queryReload->setPinSn('E000000024B00001'); // HitPoints Pin SN

    $result = $api->request($queryReload);

    print_r($result);
    // $result['data']

} catch (ApiClientException $e) {
    var_dump($e->getMessage());
} catch (ApiException $e) {
    var_dump($e->getMessage());
}
```

$result

```PHP
Array
(
    [data] => Array
        (
            [transaction_id] => 
            [pin_sn] => 
            [currency] => CNY
            [amount] => 10.00
            [pay_time] => 
            [reference_id] => 20200420102238
            [merchant_id] => 10003
            [body] => Game direct top up
            [detail] => Game top up with hitpoints currency MYR and amount=10.
            [attach] => order=12&transaction=1&user_name=bbc
            [pay_method] => web
            [result] => pending
        )

    [code] => 200
    [message] => ok
    [success] => true
)

```

**接口说明**

运营商创建支付后，可以通过该接口主动查询订单的支付结果；

特别是订单创建一段时间后，如果还没有收到HitPoints的回调通知，则通过该API查询用户的支付状态。

在该API中提供了三种查询方式：

- 通过运营商自己的交易订单ID查询(reference ID);
- 通过HitPoints的交订单ID查询(transaction ID), 这个ID会在回调通知中返回运营商；
- 通过HitPoints的卡号查询(PIN SN)，注意是卡号，不是卡密。

----

### 查询HitPoints PIN状态

```PHP
use SeaGM\HitPoints\HitPoints;
use SeaGM\HitPoints\Exception\ApiClientException;
use SeaGM\HitPoints\Exception\ApiException;

// Query pin state
try {
    $api = HitPoints::apiClient($options);
    $queryReload = $api->newPinQueryRequest();
    // Query the pin state by PIN SN
     $queryReload->setPinSn('Q000000021W00001'); // HitPoints Pin SN

    $result = $api->request($queryReload);

    print_r($result);
    // $result['data']

} catch (ApiClientException $e) {
    var_dump($e->getMessage());
} catch (ApiException $e) {
    var_dump($e->getMessage());
}
```

$result

```PHP
Array
(
    [data] => Array
        (
            [pin_sn] => Q000000021W00001
            [category_name] => Happy Card
            [product_name] => 10 Yuan
            [currency] => CNY
            [amount] => 10.00
            [status] => verified
            [create_time] => 1568026055
            [expire_time] => 1630252800
            [verify_time] => 1568026076
            [is_settled] => 1
        )

    [code] => 200
    [message] => ok
    [success] => true
)
```

**接口说明**

- 一般情况下运营商用不到这个API，因为用户在运营商平台充值时使用的是卡密。运营商如果没有收集点卡序列号则无法查询点卡状态。
- 但是有一种情况，如果用户在运营商平台充值时显示卡密异常，或卡密已使用时。运营商可以让用户提供点卡序列号，通过该API查询点卡是否已消费。**注：只有点卡是在运营商的平台消费的，运营商才能查询到该点卡信息，如果用户的点卡不是在运营商的平台上消费的，则即使运营商拿到点卡序列号也无法查到点卡状态**
- 另外：运营商还可以通过查询支付结果的API查询更详细的支付信息

---

### 直接调用API支付

```PHP
use SeaGM\HitPoints\HitPoints;
use SeaGM\HitPoints\Exception\ApiClientException;
use SeaGM\HitPoints\Exception\ApiException;

// Merchant has created trade order and call HitPoints pay.
$merchantOutTradeId = date('YmdHis');

// Directly call HitPoints pay from API
try {
    $api = HitPoints::apiClient($options);
    $pinPay = $api->newPinPayRequest();
    // set request parameters
    $pinPay->setReferenceId($merchantOutTradeId); // required
    $pinPay->setPinKey('254252L9B182R42O'); // required
    $pinPay->setCurrency('CNY'); // optional
    $pinPay->setAmount('10.00'); // optional
    $pinPay->setBody('Game direct top up'); // optional
    $pinPay->setDetail('Game top up with hitpoints currency MYR and amount=10.'); // optional
    $pinPay->setAttach('order=12&transaction=1&user_name=bbc'); // optional

    $result = $api->request($pinPay);

    print_r($result);
    // $result['data']

} catch (ApiClientException $e) {
    var_dump($e->getMessage());
} catch (ApiException $e) {
    var_dump($e->getMessage());
}
```

$result 

```PHP
Array
(
    [data] => Array
        (
            [transaction_id] => 25
            [pin_sn] => K000000022H00001
            [currency] => CNY
            [amount] => 10.00
            [pay_time] => 1587390094
            [reference_id] => 20200420134133
            [merchant_id] => 10003
            [body] => Game direct top up
            [detail] => Game top up with hitpoints currency MYR and amount=10.
            [attach] => order=12&transaction=1&user_name=bbc
            [pay_method] => api
            [result] => success
        )

    [code] => 200
    [message] => ok
    [success] => true
)
```

**接口说明**

运营商有独立运营的交易平台，并且具有一定的开发能力，对点卡的充值交互体验有自己独特的需求，运营商可以在为用户创建好充值订单后，直接从运营商的服务端调用HitPoints API完成对点卡卡密的校验与充值。

- 该API要求使用Openssl证书签名
- 运营商需要设置调用API的IP白名单
- 运营商需要为用户输入卡密的网页提供足够的安全保证

----

### 创建取卡订单取卡

```PHP
use SeaGM\HitPoints\HitPoints;
use SeaGM\HitPoints\Exception\ApiClientException;
use SeaGM\HitPoints\Exception\ApiException;

// Reseller fetch pin through API
try {
    $api = HitPoints::apiClient($options);
    $fetchPin = $api->newFetchPinRequest();

    // Set fetch pin parameters
    $fetchPin->setOutTradeId('reseller_02919328'); // required
    $fetchPin->setProductId(2); // required
    $fetchPin->setQuantity(3); // required

    $result = $api->request($fetchPin);

    print_r($result);
    // $result['data']

} catch (ApiClientException $e) {
    var_dump($e->getMessage());
} catch (ApiException $e) {
    var_dump($e->getMessage());
}
```

$result

```PHP
Array
(
    [data] => Array
        (
            [order_id] => 26
            [reseller_id] => 10007
            [out_trade_id] => reseller_02919328
            [user_account_id] => 9
            [category_name] => Happy Card
            [product_name] => 10 Yuan
            [currency] => CNY
            [product_id] => 2
            [par_value] => 10.00
            [reseller_discount] => 0.00
            [unit_price] => 10.00
            [quantity] => 3
            [total_price] => 30.00
            [status] => completed
            [create_time] => 1587391574
            [order_pins] => Array
                (
                    [0] => Array
                        (
                            [pin_sn] => E000000026F00001
                            [pin_key] => 93E1XBI38A8WSP9V
                            [order_id] => 26
                            [product_id] => 2
                            [category_name] => Happy Card
                            [product_name] => 10 Yuan
                            [currency] => CNY
                            [amount] => 10.00
                            [cost_price] => 10.00
                            [status] => active
                            [create_time] => 1587391574
                            [expire_time] => 1649606400
                        )

                    [1] => Array
                        (
                            [pin_sn] => X000000026Q00002
                            [pin_key] => 374902602W6L870C
                            [order_id] => 26
                            [product_id] => 2
                            [category_name] => Happy Card
                            [product_name] => 10 Yuan
                            [currency] => CNY
                            [amount] => 10.00
                            [cost_price] => 10.00
                            [status] => active
                            [create_time] => 1587391574
                            [expire_time] => 1649606400
                        )

                    [2] => Array
                        (
                            [pin_sn] => K000000026A00003
                            [pin_key] => M066D8J24E5O971X
                            [order_id] => 26
                            [product_id] => 2
                            [category_name] => Happy Card
                            [product_name] => 10 Yuan
                            [currency] => CNY
                            [amount] => 10.00
                            [cost_price] => 10.00
                            [status] => active
                            [create_time] => 1587391574
                            [expire_time] => 1649606400
                        )

                )

        )

    [code] => 200
    [message] => ok
    [success] => true
)
```

**接口说明**

- 代理商通过API获取产品列表，查看已开通的HitPoints点卡产品信息。
- 代理商从产品列表中，可以看到每种PIN的[product_id]以及单次最大允许取卡数量[max_fetch_quantity]
- 代理商可以通过HitPoints提供的API取卡接口，远程完成对指定product_id取卡。

----

### 查询取卡订单

```PHP
use SeaGM\HitPoints\HitPoints;
use SeaGM\HitPoints\Exception\ApiClientException;
use SeaGM\HitPoints\Exception\ApiException;

// Reseller fetch pin through API
try {
    $api = HitPoints::apiClient($options);
    $orderQuery = $api->newOrderQueryRequest();

    // Set order query parameters
    $orderQuery->setOutTradeId('reseller_02919328'); // required

    $result = $api->request($orderQuery);

    print_r($result);
    // $result['data']

} catch (ApiClientException $e) {
    var_dump($e->getMessage());
} catch (ApiException $e) {
    var_dump($e->getMessage());
}
```

$result

```PHP
Array
(
    [data] => Array
        (
            [order_id] => 26
            [reseller_id] => 10007
            [out_trade_id] => reseller_02919328
            [user_account_id] => 9
            [category_name] => Happy Card
            [product_name] => 10 Yuan
            [currency] => CNY
            [product_id] => 2
            [par_value] => 10.00
            [reseller_discount] => 0.00
            [unit_price] => 10.00
            [quantity] => 3
            [total_price] => 30.00
            [status] => completed
            [create_time] => 1587391574
            [order_pins] => Array
                (
                    [0] => Array
                        (
                            [pin_sn] => E000000026F00001
                            [pin_key] => 93E1XBI38A8WSP9V
                            [order_id] => 26
                            [product_id] => 2
                            [category_name] => Happy Card
                            [product_name] => 10 Yuan
                            [currency] => CNY
                            [amount] => 10.00
                            [cost_price] => 10.00
                            [status] => active
                            [create_time] => 1587391574
                            [expire_time] => 1649606400
                        )

                    [1] => Array
                        (
                            [pin_sn] => X000000026Q00002
                            [pin_key] => 374902602W6L870C
                            [order_id] => 26
                            [product_id] => 2
                            [category_name] => Happy Card
                            [product_name] => 10 Yuan
                            [currency] => CNY
                            [amount] => 10.00
                            [cost_price] => 10.00
                            [status] => active
                            [create_time] => 1587391574
                            [expire_time] => 1649606400
                        )

                    [2] => Array
                        (
                            [pin_sn] => K000000026A00003
                            [pin_key] => M066D8J24E5O971X
                            [order_id] => 26
                            [product_id] => 2
                            [category_name] => Happy Card
                            [product_name] => 10 Yuan
                            [currency] => CNY
                            [amount] => 10.00
                            [cost_price] => 10.00
                            [status] => active
                            [create_time] => 1587391574
                            [expire_time] => 1649606400
                        )

                )

        )

    [code] => 200
    [message] => ok
    [success] => true
)
```

**接口说明**

- 代理商可以通过HitPoints的API取卡后，代理商还可以通过取卡的out_trade_id再次查询取卡记录。

----

### 处理HitPoints支付异步回调通知

```PHP
use SeaGM\HitPoints\HitPoints;
use SeaGM\HitPoints\Exception\ApiClientException;
use SeaGM\HitPoints\Exception\ApiException;

try {
    $api = HitPoints::apiClient($options);
    // notifiy callback data; type is array
    $data = $api->getNotification();

    $reference_id = $data['reference_id'];

    // TODO get order by reference ID and validate the order data
    //$order = getOrderByReferenceId($reference_id);
    // TODO use DB Transaction to avoid concurrent/repeated notification request. 注意处理重复并发通知
    // if ($order['state'] === 'pending_pay' && $data['result'] === 'success' && $data['order_amount'] == $data['pay_amount']) {
        // TODO modify order state to paid and recharge to players account

    //}
    // TODO act HitPoints after handle order state
    $api->ackNotificationReceived();

} catch (ApiClientException $e) {
    var_dump($e->getMessage());
} catch (ApiException $e) {
    var_dump($e->getMessage());
}
```

Notify request body

```PHP
{
	"transaction_id": 26,
	"pin_sn": "W000000023Y00001",
	"currency": "CNY",
	"amount": "10.00",
	"pay_time": 1587438324,
	"reference_id": "20200421030349",
	"merchant_id": 10003,
	"body": "Game direct top up",
	"detail": "Game top up with hitpoints currency MYR and amount=10.",
	"attach": "order=12&transaction=1&user_name=bbc",
	"pay_method": "web",
	"result": "success"
}
```

**接口说明**

回调通知将以POST方式请求运营商提供的回调网址，请求的数据格式是json。

该SDK通过 `getNotification()`方法获取回调请求的结果，该方法返回的结果为已经通过数据签名验证的结果。

并且，该方法是将json解析为数组的形式返回。运营商开发者自行验证回调通知中的订单信息。

其中 result = success 表示已支付。