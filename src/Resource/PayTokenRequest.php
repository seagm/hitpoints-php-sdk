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
     * If publisher customized multiple card types
     * can specify the card type that the user is allowed to use
     * Default = hitpoints
     * if you need to specify multiples, just separate each type with commas,
     * e.g. gift-card,hitpoints,game-pin
     *
     * @var string
     */
    protected $card_type;

    /**
     * Specify the category ids that you forbid user to use for the transaction
     * multi-category, concat with comma, eg. 1234,1223
     * @var string
     */
    protected $exclude_category;

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

    /**
     * @param string $cardType
     */
    public function setCardType($cardType)
    {
        $this->card_type = $cardType;
    }

    /**
     * @param string $exclude_category
     */
    public function setExcludeCategory($exclude_category)
    {
        $this->exclude_category = $exclude_category;
    }

}
