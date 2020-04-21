<?php
// 处理HitPoints回调通知
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

try {
    $api = HitPoints::apiClient($options);
    $data = $api->getNotification();
    // {"transaction_id":26,"pin_sn":"W000000023Y00001","currency":"CNY","amount":"10.00","pay_time":1587438324,"reference_id":"20200421030349","merchant_id":10003,"body":"Game direct top up","detail":"Game top up with hitpoints currency MYR and amount=10.","attach":"order=12&transaction=1&user_name=bbc","pay_method":"web","result":"success"}

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