<?php
// 点卡状态查询
use SeaGM\HitPoints\HitPoints;
use SeaGM\HitPoints\Exception\ApiClientException;
use SeaGM\HitPoints\Exception\ApiException;

include '../vendor/autoload.php';
$options = [
    'app_key' => 'gIozQaiZ1zI9_',
    'app_secret' => '_v40Mvz91jdaU6o7DMDRR-HS_',
    'api_base' => 'http://onepin.seagm.test',
//    'rsa_private_key_file' => './cert/rsa_private_key.pem',
//    'rsa_server_key_file' => './cert/rsa_server_key.pem'
];


// Query pin state
try {
    $api = HitPoints::apiClient($options);
    $queryReload = $api->newRevokePinRequest();
    // Query the pin state by PIN SN
     $queryReload->setPinSn('J000010024H00002'); // HitPoints Pin SN

    $result = $api->request($queryReload);

    print_r($result);
    // $result['data']

} catch (ApiClientException $e) {
    var_dump($e->getMessage());
} catch (ApiException $e) {
    var_dump($e->getMessage());
}
