<?php
// 点卡状态查询
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