<?php
// 创建取卡订单取卡
use SeaGM\HitPoints\HitPoints;
use SeaGM\HitPoints\Exception\ApiClientException;
use SeaGM\HitPoints\Exception\ApiException;

include '../vendor/autoload.php';
$options = [
    'app_key' => 'gIoyQaaZ1zICMtJoXYOrw22o',
    'app_secret' => 'x-8zNab91jd77V7cPx5hq62jh9F_kz4GK8NeoLszLTCkGg',
    'api_base' => 'http://api.hitpoints.test',
    'rsa_private_key_file' => '',
    'rsa_server_key_file' => ''
];

// Reseller fetch pin through API
try {
    $api = HitPoints::apiClient($options);
    $fetchPin = $api->newFetchPinRequest();

    // Set fetch pin parameters
    $fetchPin->setOutTradeId('reseller_02919329'); // required
    $fetchPin->setProductId(4); // required
    $fetchPin->setQuantity(5); // required

    $result = $api->request($fetchPin);
    print_r($result);
    // $result['data']

} catch (ApiClientException $e) {
    var_dump($e->getMessage());
} catch (ApiException $e) {
    var_dump($e->getMessage());
}