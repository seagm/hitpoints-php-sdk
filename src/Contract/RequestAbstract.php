<?php

namespace SeaGM\HitPoints\Contract;

use Ramsey\Uuid\Uuid;
use SeaGM\HitPoints\Concern\ApiTrait;
use SeaGM\HitPoints\HitPoints;
use SeaGM\HitPoints\Lib\RequestMethod;

/**
 * Class RequestAbstract
 * @package SeaGM\HitPoints\Contract
 */
abstract class RequestAbstract implements ParameterInterface
{
    use ApiTrait;

    /**
     * Api request method, make it override in entity class
     *
     * @var string
     */
    protected $requestMethod = RequestMethod::GET;

    /**
     * Api request uri, make it override in entity class
     *
     * @var string
     */
    protected $apiUri = '';

    /**
     * Random string
     *
     * @var string
     */
    protected $random_key;

    /**
     * get api request parameters from api request instance
     *
     * @return array
     * @throws \Exception
     */
    public function getApiParameters()
    {
        if(!$this->random_key){
            $this->random_key = Uuid::uuid4()->toString();
        }
        $params = get_object_vars($this);
        unset($params['apiUri'], $params['requestMethod']);
        return $params;
    }

    /**
     * create http headers for api request
     * Get apiParameters and makeSignature
     *
     * @return array
     * eg. [
     *      'Content-Type'=>'application/x-www-form-urlencoded',
     *      'APPID'=>'Alxi123Io9',
     *      'Sign'=>'signatureStringHere',
     *      'Date-GMT'=>'Wed, 04 Sep 2019 02:50:59 GMT'
     * ]
     * @throws \Exception
     */
    public function createRequestHeaders()
    {
        $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $headers['APPID'] = HitPoints::getAppKey();

        $signDate = $this->currentGmtTime();
        $headers['Date-GMT'] = $signDate;

        $apiParameters = $this->getApiParameters();
        $signature = $this->makeSignature($apiParameters, $signDate, HitPoints::getAppSecret());

        $headers['Sign'] = $signature;
        return $headers;
    }

    /**
     * @return mixed
     */
    public function getApiUri()
    {
        return $this->apiUri;
    }

    /**
     * @return mixed
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

}