<?php
// 通过API发起点卡充值
use SeaGM\HitPoints\HitPoints;
use SeaGM\HitPoints\Exception\ApiClientException;
use SeaGM\HitPoints\Exception\ApiException;

include '../vendor/autoload.php';
$options = [
    'app_key' => 'gIoyQaKZ1zIQqBVlzR_BxrJ4',
    'app_secret' => 'yelyHt6Y0jRkeXwFDiMmA-APSWj88eELzkvIxN6ZS1MHgWET',
    'api_base' => 'http://api.hitpoints.test',
    'rsa_private_key_file' => './cert/rsa_private_key.pem',
    'rsa_server_key_file' => './cert/rsa_server_key.pem'
];

// Merchant has created trade order and call HitPoints pay.
$merchantOutTradeId = date('YmdHis');

// Directly call HitPoints pay from API
try {
    $api = HitPoints::apiClient($options);
    $pinPay = $api->newPinPayRequest();
    // set request parameters
    $pinPay->setReferenceId($merchantOutTradeId); // required
    $pinPay->setPinKey('69817C10O869104M'); // required
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