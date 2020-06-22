<?php

namespace SeaGM\HitPoints\Resource;

use SeaGM\HitPoints\Contract\RequestAbstract;
use SeaGM\HitPoints\Lib\RequestMethod;

/**
 * Class PayTokenRequest
 * @package SeaGM\HitPoints\Resource
 */
class PayTokenRequest extends RequestAbstract
{
    protected $apiUri = '/v1/reload/create';

    protected $requestMethod = RequestMethod::POST;

    /**
     * Merchant self transaction id, keep unique
     * It's required and max length is 45
     * @var string
     */
    protected $reference_id;

    /**
     * Merchant transaction description
     * It's optional and max length is 128
     * @var string
     */
    protected $body;

    /**
     * Merchant transaction detail
     * It's optional and max length is 1000
     * @var string
     */
    protected $detail;

    /**
     * Merchant attached message with the payment, it will be return to merchant in callback
     * It's optional and max length is 128
     * @var string
     */
    protected $attach;

    /**
     * Payment currency, currency code
     * It's optional.
     * HitPoints will check the user's HitPoints pin's currency with the specified currency
     * @var string
     */
    protected $currency;

    /**
     * Payment amount, means the face value of the HitPoints Pin
     * It's optional.
     * HitPoints will check the user's HitPoints pin's face-value with the specified amount
     * @var string
     */
    protected $amount;

    /**
     * HitPoints will jump back to user specified url address
     * It's optional and max length is 255
     * @var string
     */
    protected $return_url;

    /**
     * @param string $reference_id
     */
    public function setReferenceId($reference_id)
    {
        $this->reference_id = $reference_id;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @param string $detail
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;
    }

    /**
     * @param string $attach
     */
    public function setAttach($attach)
    {
        $this->attach = $attach;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @param string $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @param string $return_url
     */
    public function setReturnUrl($return_url)
    {
        $this->return_url = $return_url;
    }

}