<?php

namespace SeaGM\HitPoints\Contract;

use GuzzleHttp\Client;
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
        $httpClient = new Client(['timeout' => 60, 'base_uri' => HitPoints::getApiUrlBase()]);
        try {
            switch ($requestMethod) {
                case RequestMethod::POST:
                    $response = $httpClient->request(RequestMethod::POST, $apiUri, [
                        'form_params' => $apiParameters,
                        'headers' => $headers
                    ]);
                    break;
                case RequestMethod::GET:
                    $response = $httpClient->request(RequestMethod::GET, $apiUri, [
                        'query' => $apiParameters,
                        'headers' => $headers
                    ]);
                    break;
                default:
                    throw new ApiClientException('invalid request method');
            }
            $this->apiStatusCode = $response->getStatusCode();
            if ($this->apiStatusCode !== 200) {
                throw new ApiException('Invalid http response status');
            }
            $this->apiResponseBody = $response->getBody()->getContents();
            $this->apiResponseHeaders = $response->getHeaders();
            return $this->parseResponse();
        } catch (ApiClientException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
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
        $responseHeader = $this->getApiResponseHeaders();
        $dataSignature = isset($responseHeader['Sign'][0]) ? $responseHeader['Sign'][0] : '';
        if (!$dataSignature) {
            throw new ApiException('Response missing signature');
        }
        $signDate = isset($responseHeader['Date-GMT'][0]) ? $responseHeader['Date-GMT'][0] : '';
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
        $headers = $this->getNotificationHeaders();
        $signDate = isset($headers['Date-Gmt']) ? trim($headers['Date-Gmt']) : '';
        $dataSignature = isset($headers['Sign']) ? trim($headers['Sign']) : '';
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
