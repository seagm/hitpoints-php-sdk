<?php

namespace SeaGM\HitPoints\Contract;
/**
 * Interface ParameterInterface
 * @package SeaGM\HitPoints\Contract
 */
interface ParameterInterface
{

    /**
     * get api request parameters from api request instance
     *
     * @return array
     */
    public function getApiParameters();

    /**
     * make data signature
     *
     * @param array $apiParameters
     * @param string $signDateGMT
     * @param string $appSecret
     *
     * @return string signature
     */
    public function makeSignature(array $apiParameters, $signDateGMT, $appSecret);

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
     */
    public function createRequestHeaders();

    /**
     * Get Api Uri, eg. '/v1/items'
     *
     * @return string
     */
    public function getApiUri();

    /**
     * Get request method, eg. 'POST'
     *
     * @return string
     */
    public function getRequestMethod();

}