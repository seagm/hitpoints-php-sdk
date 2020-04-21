<?php
// 查询取卡订单
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