<?php
// 创建点卡支付token，通过HitPoints的安全支付页面完成支付(推荐)
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
$paySuccessPageUrl = 'https://merchant.example.com/order/success?transaction=' . $merchantOutTradeId;

try {
    $api = HitPoints::apiClient($options);
    $payToken = $api->newPayTokenRequest();
    // set request parameters
    $payToken->setReferenceId($merchantOutTradeId); // required
    $payToken->setReturnUrl($paySuccessPageUrl); // optional
    $payToken->setCurrency('CNY'); // optional
    //$payToken->setAmount('10.00'); // optional
    $payToken->setBody('Game direct top up'); // optional
    $payToken->setDetail('Game top up with hitpoints currency MYR and amount=10.'); // optional
    $payToken->setAttach('order=12&transaction=1&user_name=bbc'); // optional

    $result = $api->request($payToken);

    print_r($result);
    // $result['data']

} catch (ApiClientException $e) {
    var_dump($e->getMessage());
} catch (ApiException $e) {
    var_dump($e->getMessage());
}