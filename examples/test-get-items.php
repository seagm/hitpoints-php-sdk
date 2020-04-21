<?php
// 查询已授权/开通的产品
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

