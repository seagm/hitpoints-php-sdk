<?php
// 充值查询
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