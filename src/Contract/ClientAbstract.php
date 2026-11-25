<?php

namespace SeaGM\HitPoints\Contract;

use SeaGM\HitPoints\Concern\ApiTrait;
use SeaGM\HitPoints\Exception\ApiClientException;
use SeaGM\HitPoints\Exception\ApiException;
use SeaGM\HitPoints\HitPoints;
use SeaGM\HitPoints\Lib\RequestMethod;

/**
 * Class ClientAbstract
 * @package SeaGM\HitPoints\Contract
 */
abstract class ClientAbstract implements ApiInterface, NotificationInterface
{
    use ApiTrait;

    protected $appKey;
    protected $appSecret;
    protected $apiUrlBase;
    protected $rsaPrivateKey;
    protected $rsaServerKey;
    protected $rsaKeyEnabled = false;

    protected $apiStatusCode;
    protected $apiResponseBody;
    protected $apiResponseHeaders = [];

    /**
     * ApiHandler constructor.
     * @param array $options
     * @throws ApiClientException
     */
    public function __construct(array $options)
    {
        $this->appKey = isset($options['app_key']) ? $options['app_key'] : '';
        $this->appSecret = isset($options['app_secret']) ? $options['app_secret'] : '';
        $this->apiUrlBase = isset($options['api_base']) ? $options['api_base'] : '';
        if (!$this->appKey || !$this->appSecret || !$this->apiUrlBase) {
            throw new ApiClientException('HitPoints API Client config error, please check config parameters');
        }
        if (isset($options['rsa_private_key_file']) && !empty($options['rsa_private_key_file'])) {
            if (!file_exists($options['rsa_private_key_file'])) {
                throw new ApiClientException('Rsa private key file not exists:' . $options['rsa_private_key_file']);
            }
            $this->rsaPrivateKey = openssl_pkey_get_private(file_get_contents($options['rsa_private_key_file']));
            if (!$this->rsaPrivateKey) {
                throw new ApiClientException('Read rsa private key fail');
            }

            if (!isset($options['rsa_server_key_file']) || !$options['rsa_server_key_file']) {
                throw new ApiClientException('Rsa server key file not config');
            }
            if (!file_exists($options['rsa_server_key_file'])) {
                throw new ApiClientException('Rsa server key file not exists:' . $options['rsa_server_key_file']);
            }
            $this->rsaServerKey = openssl_pkey_get_public(file_get_contents($options['rsa_server_key_file']));
            if (!$this->rsaServerKey) {
                throw new ApiClientException('Read rsa server key fail');
            }

            $this->rsaKeyEnabled = true;
        }
    }

    /**
     * @param ParameterInterface $requestData
     * @return mixed
     * @throws ApiClientException
     * @throws ApiException
     */
    public function request(ParameterInterface $requestData)
    {
        $apiParameters = $requestData->getApiParameters();
        $headers = $requestData->createRequestHeaders();
        $requestMethod = $requestData->getRequestMethod();
        $apiUri = $requestData->getApiUri();
        try {
            switch ($requestMethod) {
                case RequestMethod::POST:
                case RequestMethod::GET:
                    break;
                default:
                    throw new ApiClientException('invalid request method');
            }
            $response = $this->sendHttpRequest($requestMethod, $apiUri, $apiParameters, $headers);
            $this->apiStatusCode = $response['statusCode'];
            if ($this->apiStatusCode !== 200) {
                throw new ApiException('Invalid http response status');
            }
            $this->apiResponseBody = $response['body'];
            $this->apiResponseHeaders = $response['headers'];
            return $this->parseResponse();
        } catch (ApiClientException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Send http request with the native curl extension
     *
     * @param string $requestMethod
     * @param string $apiUri
     * @param array $apiParameters
     * @param array $headers
     * @return array ['statusCode' => int, 'body' => string, 'headers' => array]
     * @throws ApiException
     */
    private function sendHttpRequest($requestMethod, $apiUri, array $apiParameters, array $headers)
    {
        if (!function_exists('curl_init')) {
            throw new ApiException('cURL extension is required by HitPoints API Client');
        }

        $responseHeaders = [];
        $isPost = $requestMethod === RequestMethod::POST;
        $url = $this->buildRequestUrl($apiUri, $isPost ? [] : $apiParameters);
        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTPHEADER => $this->formatRequestHeaders($headers),
            CURLOPT_HEADERFUNCTION => function ($curl, $headerLine) use (&$responseHeaders) {
                $line = trim($headerLine);
                if (stripos($line, 'HTTP/') === 0) {
                    // new response block, e.g. redirect or "100 Continue", drop previous headers
                    $responseHeaders = [];
                } elseif (strpos($line, ':') !== false) {
                    list($name, $value) = explode(':', $line, 2);
                    // keep the previous header shape: header name => list of values
                    $responseHeaders[trim($name)][] = trim($value);
                }
                return strlen($headerLine);
            },
        ];
        if ($isPost) {
            $curlOptions[CURLOPT_POST] = true;
            $curlOptions[CURLOPT_POSTFIELDS] = http_build_query($apiParameters);
        } else {
            $curlOptions[CURLOPT_HTTPGET] = true;
        }

        $curl = curl_init();
        curl_setopt_array($curl, $curlOptions);
        $body = curl_exec($curl);
        if ($body === false) {
            $errorMessage = curl_error($curl);
            $errorCode = curl_errno($curl);
            curl_close($curl);
            throw new ApiException('Http request fail: ' . $errorMessage, $errorCode);
        }
        $statusCode = (int)curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);

        return [
            'statusCode' => $statusCode,
            'body' => (string)$body,
            'headers' => $responseHeaders,
        ];
    }

    /**
     * Build the full request url, resolving the api uri against the configured api base
     *
     * @param string $apiUri
     * @param array $queryParameters
     * @return string
     */
    private function buildRequestUrl($apiUri, array $queryParameters)
    {
        $baseUrl = $this->getApiUrlBase();
        if (preg_match('#^https?://#i', $apiUri)) {
            $url = $apiUri;
        } elseif (substr($apiUri, 0, 1) === '/') {
            // an absolute path replaces the path of the base url, same as the previous base_uri rule
            $baseParts = parse_url($baseUrl);
            $url = isset($baseParts['scheme']) ? $baseParts['scheme'] . '://' : '';
            $url .= isset($baseParts['host']) ? $baseParts['host'] : '';
            $url .= isset($baseParts['port']) ? ':' . $baseParts['port'] : '';
            $url .= $apiUri;
        } else {
            $url = rtrim($baseUrl, '/') . '/' . ltrim($apiUri, '/');
        }

        if (!empty($queryParameters)) {
            $queryString = http_build_query($queryParameters, '', '&', PHP_QUERY_RFC3986);
            if ($queryString !== '') {
                $url .= (strpos($url, '?') === false ? '?' : '&') . $queryString;
            }
        }
        return $url;
    }

    /**
     * Convert header array to curl header lines
     *
     * @param array $headers
     * @return array
     */
    private function formatRequestHeaders(array $headers)
    {
        $headerLines = [];
        foreach ($headers as $name => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $headerLines[] = $name . ': ' . $value;
        }
        return $headerLines;
    }

    /**
     * @return mixed
     * @throws ApiException
     * @throws ApiClientException
     */
    private function parseResponse()
    {
        if (!$this->apiResponseBody) {
            throw new ApiException('Api error,response body');
        }
        $data = json_decode($this->apiResponseBody, true);
        if (!$data) {
            throw new ApiException('Api error,response body decode fail');
        }
        if (!isset($data['code'])) {
            throw new ApiException('Api error, response missing code parameter');
        }
        if ($data['code'] !== 200) {
            $apiErrorMessage = isset($data['message']) ? $data['message'] : 'Api error, no message response';
            throw new ApiException($apiErrorMessage, (int)$data['code']);
        }
        $responseData = isset($data['data']) ? $data['data'] : [];
        if (!empty($responseData)) {
            $this->validateResponseSignature($responseData);
        }
        return $data;
    }

    /**
     * @param $data
     * @return bool
     * @throws ApiException
     * @throws ApiClientException
     */
    private function validateResponseSignature($data)
    {
        $responseHeader = $this->formatHeaders($this->getApiResponseHeaders());
        $dataSignature = isset($responseHeader['sign'][0]) ? $responseHeader['sign'][0] : '';
        if (!$dataSignature) {
            throw new ApiException('Response missing signature');
        }
        $signDate = isset($responseHeader['date-gmt'][0]) ? $responseHeader['date-gmt'][0] : '';
        if (!$signDate) {
            throw new ApiException('Response missing Date-GMT');
        }

        if ($this->isRsaKeyEnabled()) {
            $dataToString = $this->convertToString($data);
            $rawString = $dataToString . $signDate;
            if (openssl_verify($rawString, base64_decode($dataSignature), HitPoints::getRsaServerKey())) {
                return true;
            }
            throw new ApiException('Data signature validate fail with rsa');
        }

        $calSignature = $this->makeSignature($data, $signDate, $this->getAppSecret());
        if ($calSignature !== $dataSignature) {
            throw new ApiException('Data signature validate fail with secret');
        }
        return true;
    }

    /**
     * Get HitPoints Callback notification
     *
     * @return array
     * @throws ApiException
     * @throws ApiClientException
     */
    public function getNotification()
    {
        $rawString = file_get_contents('php://input');
        $data = json_decode($rawString, true);
        if (!$data) {
            throw new ApiException('Notification data format error');
        }
        $headers = $this->formatHeaders($this->getNotificationHeaders());
        $signDate = isset($headers['date-gmt']) ? trim($headers['date-gmt']) : '';
        $dataSignature = isset($headers['sign']) ? trim($headers['sign']) : '';
        if (!$dataSignature || !$signDate) {
            throw new ApiException('Notification missing data signature');
        }

        $dataToString = $this->convertToString($data);
        if ($this->isRsaKeyEnabled()) {
            $rawString = $dataToString . $signDate;
            if (!openssl_verify($rawString, base64_decode($dataSignature), HitPoints::getRsaServerKey())) {
                throw new ApiException('Data signature validate fail with rsa');
            }
        }

        $calSignature = $this->makeSignature($data, $signDate, $this->getAppSecret());
        if ($calSignature !== $dataSignature) {
            throw new ApiException('Data signature validate fail with secret');
        }

        return $data;
    }

    /**
     * change header key to lowercase
     * @param $headers
     * @return array
     */
    public function formatHeaders($headers)
    {
        $formatted = [];
        if (is_array($headers)) {
            foreach ($headers as $key => $val) {
                $formatted[strtolower($key)] = $val;
            }
        }
        return $formatted;
    }

    /**
     * Response HitPoints that the notification has received
     *
     * @return void
     */
    public function ackNotificationReceived()
    {
        echo 'success';
    }

    /**
     * @return mixed|string
     */
    public function getAppKey()
    {
        return $this->appKey;
    }

    /**
     * @return mixed|string
     */
    public function getAppSecret()
    {
        return $this->appSecret;
    }

    /**
     * @return mixed|string
     */
    public function getApiUrlBase()
    {
        return $this->apiUrlBase;
    }


    /**
     * @return mixed
     */
    public function getApiStatusCode()
    {
        return $this->apiStatusCode;
    }

    /**
     * @return mixed
     */
    public function getApiResponseBody()
    {
        return $this->apiResponseBody;
    }

    /**
     * @return array
     */
    public function getApiResponseHeaders()
    {
        return $this->apiResponseHeaders;
    }

    /**
     * @return mixed
     */
    public function getRsaPrivateKey()
    {
        return $this->rsaPrivateKey;
    }

    /**
     * @return bool
     */
    public function isRsaKeyEnabled()
    {
        return $this->rsaKeyEnabled;
    }

    /**
     * @return mixed
     */
    public function getRsaServerKey()
    {
        return $this->rsaServerKey;
    }
}
