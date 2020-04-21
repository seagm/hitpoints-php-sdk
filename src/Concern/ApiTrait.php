<?php

namespace SeaGM\HitPoints\Concern;

use SeaGM\HitPoints\Exception\ApiClientException;
use SeaGM\HitPoints\HitPoints;

/**
 * Trait ApiTrait
 * @package SeaGM\HitPoints\Conern
 */
trait ApiTrait
{
    /**
     * @param array $data
     * @param string $dateGMT
     * @param string $appSecret
     * @return string
     * @throws ApiClientException
     */
    public function makeSignature(array $data, $dateGMT, $appSecret)
    {
        $dataToString = $this->convertToString($data);
        $rawString = $dataToString . $dateGMT;
        if (HitPoints::isRsaKeyEnabled()) {
            $privateKey = HitPoints::getRsaPrivateKey();
            if (!$privateKey) {
                throw new ApiClientException('get rsa private key fail');
            }
            openssl_sign($rawString, $signature, $privateKey);
            if ($signature) {
                $signature = base64_encode($signature);
                return $signature;
            }
            throw new ApiClientException('Rsa signature fail');
        }

        return base64_encode(hash_hmac('sha256', $rawString, $appSecret, true));
    }

    /**
     * @return string
     */
    public function currentGmtTime()
    {
        return gmdate("D, d M Y H:i:s", time()) . " GMT";
    }

    /**
     * @param array $params
     * @return string
     */
    private function convertToString(array $params)
    {
        if (!is_array($params) || empty($params)) {
            return '';
        }
        $signArray = [];
        if (is_array($params) && $this->_isArrayIndexContinuous($params)) {
            foreach ($params as $k => $v) {
                if (is_array($v)) {
                    $signArray[] = $this->convertToString($v);
                } else {
                    $signArray[] = (string)$v;
                }
            }
            asort($signArray);
        } else {
            ksort($params);
            foreach ($params as $k => $v) {
                if (is_array($v)) {
                    $signArray[] = $this->convertToString($v);
                } else {
                    $signArray[] = (string)$v;
                }
            }
        }
        return implode('', $signArray);
    }

    /**
     * Check if array indexes is continuous
     * @param array $arr
     * @return bool
     */
    private function _isArrayIndexContinuous(array $arr)
    {
        $keys = array_keys($arr);
        return $keys === array_keys($keys);
    }

    /**
     * @return array|false|string
     */
    private function getNotificationHeaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}
