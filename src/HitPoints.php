<?php

namespace SeaGM\HitPoints;

use SeaGM\HitPoints\Exception\ApiClientException;

/**
 * Class HitPoints
 * @package SeaGM\HitPoints
 * @method static string getAppKey()
 * @method static string getAppSecret()
 * @method static string getApiUrlBase()
 * @method static bool isRsaKeyEnabled()
 * @method static mixed getRsaPrivateKey();
 * @method static mixed getRsaServerKey();
 * @method static string getApiResponseBody();
 * @method static array getApiResponseHeaders();
 */
class HitPoints
{
    private static $_apiClient;

    /**
     * @param array $options
     * @return ApiClient
     * @throws ApiClientException
     */
    public static function apiClient(array $options)
    {
        if (!self::$_apiClient) {
            self::$_apiClient = new ApiClient($options);
        }

        return self::$_apiClient;
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws ApiClientException
     */
    public static function __callStatic($method, $arguments)
    {
        if (!self::$_apiClient) {
            throw new ApiClientException('Api Client not initialized');
        }

        return self::$_apiClient->{$method}(...$arguments);
    }
}
