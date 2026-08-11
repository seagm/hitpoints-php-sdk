<?php
// 点卡状态查询
use SeaGM\HitPoints\HitPoints;
use SeaGM\HitPoints\Exception\ApiClientException;
use SeaGM\HitPoints\Exception\ApiException;

include '../vendor/autoload.php';
$options = [
    'app_key' => 'gIozQaiZ1zI9_xzFt9H3n2OY',
    'app_secret' => '_v40Mvz91jdaU6o7DMDRR-HS_fxNWVcL8L6LvgWbl4VdjA',
    'api_base' => 'http://onepin.seagm.test',
//    'rsa_private_key_file' => './cert/rsa_private_key.pem',
//    'rsa_server_key_file' => './cert/rsa_server_key.pem'
];


// Query pin state
try {
    $api = HitPoints::apiClient($options);
    $queryReload = $api->newRevokePinRequest();
    // Query the pin state by PIN SN
     $queryReload->setPinSn('A000010024W00001'); // HitPoints Pin SN

    $result = $api->request($queryReload);

    print_r($result);
    // $result['data']

} catch (ApiClientException $e) {
    var_dump($e->getMessage());
} catch (ApiException $e) {
    var_dump($e->getMessage());
}
