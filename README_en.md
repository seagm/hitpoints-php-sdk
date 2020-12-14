## HitPoints API PHP SDK

The SDK encapsulates the method of calling HitPoints API from the web server


## Install

add to composer.json

```json
"repositories": {
    "seagm-libs/hitpoints": {
      "type": "vcs",
      "url": "git@github.com:seagm/hitpoints-php-sdk.git"
    }
  }
```

or run the script below in your project root direction.

```
composer config repositories.seagm-libs/hitpoints vcs git@github.com:seagm/hitpoints-php-sdk.git
```

require to install the sdk code

```text
composer require seagm-libs/hitpoints
```


## Use Examples

### configs

```PHP
// config examples
$options = [
    'app_key' => 'gIoyQaaZ1zICMtJoXYOrw22o',
    'app_secret' => '8zNab91jd77V7cPx5hq62jh9F_kz4GK8NeoLszLTCkGg',
    'api_base' => 'http://open-api.hitpoints.test',
    'rsa_private_key_file'=>'',
    'rsa_server_key_file' =>''
];
```


config parameter description:

| Parameters   | Description                        | Example                      |
| ---------- | ------------------------------- | --------------------------- |
| App_key | HitPoints Developer AppID      |  |
| App_secret | HitPoints Developer AppSecret |                             |
| Api_base | HitPoints API URL | https://open-api.hitpoints.com |
| rsa_private_key_file | Rsa private key file path | /var/cert/rsa_private_key.pem |
| rsa_server_key_file | Rsa HitPoints API public key file path | /var/cert/rsa_hitpoints_key.pem |

**Remark**

If you didn't enable rsa signature，please keep rsa_private_key_file parameter empty.

If you enabled the rsa_private_key_file, then all request and response data will be signed with openssl_sign, to validate the signature, you need to get the public_key_file of HitPoints server and send your public_key_file to HitPoints.


---

### Query Signed Product List

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

**API Description**

- $result['data'] is an associative array, the data structure is as below

  ```PHP
  $result['data'] = [
  		'item-category-key-1'=>[], // category-1 product list
  		'item-category-key-2'=>[], // category-2 product list
  ]
  ```

----

### Create payment token

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

**API Description**

This api will create payment token and return the HitPoints payment page url

The process for merchant to use HitPoints Recharge is: 
1. Request API(create payment token) to get payment url; 
2. Direct users to the payment verification page of HitPoints.

When user finished HitPoints PIN verification, HitPoints will send notification request(callback)to merchant's callback_url，and if merchant has set the (return_url)，HitPoints will direct user to merchant's return_url.


---

### Query Payment Result

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

**API Description**

After created payment token，merchant can query payment result through this API；

Especially after the order is created for a period, and the callback notification from HitPoints has not been received, the user's payment status should be queried through the API.

Three query methods are provided in the API：

- Query by merchant's orderId(reference ID);
- Query by HitPoints orderID(transaction ID), the Id will be sent to merchant in the callback notification；
- Query by HitPoints card serial number(PIN SN, **Not Secret**)

----

### Query PIN Status

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

**API Description**

- Under normal circumstances, merchant would not use this API because users use the card secret when recharging on the merchant's platform. If the merchant does not collect the point card serial number, the point card status cannot be checked.
- However, there is a situation, if the user complain that the card secret is invalid when recharging on the merchant's platform, or the card secret has been verified. The merchant may get the point card serial number from the user, and check whether the point card has been consumed through the API.**Note：the merchant can only query the card status which is consumed on merchant's own platform.**
- Besides, merchants can also query more detailed payment information through the API (**Query Payment Result**)

---

### Direct Pay through API

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

**API Description**

Merchants have independently operated trading platforms and have certain development capabilities. They have their own unique requirements for the interactive experience of recharging point cards. Merchants can directly call HitPoints from the merchant’s server after creating recharge orders for users. API completes the verification and recharge of the card secret.

- openssl_sign is recommended in this API
- the merchants needs to set the IP whitelist for calling API
- merchants need to provide adequate security guarantees for the web pages where users enter the card secret

----

### Reseller Fetch Card

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

**Api Description**

- The resellers fetch the product list through API, and checks the product information of the opened HitPoints card.
- From the product list, the reseller can get the [product_id] of each PIN and the maximum allowable number of card in each fetching order [max_fetch_quantity]
- Resellers can retrieve the card for the specified product_id through the API

----

### Reseller Query Fetch Card Order

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

**API Description**

- after the reseller created the fetching card order，reseller can query the order result by out_trade_id(reseller's orderId)

----

### Handle the HitPoints payment asynchronous callback notification

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
    // TODO use DB Transaction to avoid concurrent/repeated notification request. Pay attention to handling duplicate concurrent notifications
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

**API Description**

The callback notification will request the callback URL provided by the merchant in POST method. The requested data format is json.

Through the method `getNotification()` to get the payment result，the result returned by the method has been checked data signature.

And, this method parses json into an array and returns it. Merchant developers verify the order information in the callback notification by themselves.

Where result = success means paid.
