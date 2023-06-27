<?php

namespace SeaGM\HitPoints\Resource;

use SeaGM\HitPoints\Contract\RequestAbstract;
use SeaGM\HitPoints\Lib\RequestMethod;

/**
 * Class PinPayRequest
 * @package SeaGM\HitPoints\Resource
 */
class PinPayRequest extends RequestAbstract
{
    protected $apiUri = '/v1/pin/pay';

    protected $requestMethod = RequestMethod::POST;

    /**
     * Merchant self transaction id, keep unique
     * It's required and max length is 45
     * @var string
     */
    protected $reference_id;

    /**
     * HitPoints Pin Secret KEY
     * It's required
     * @var string
     */
    protected $pin_key;

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
     * @param string $pin_key
     */
    public function setPinKey($pin_key)
    {
        $this->pin_key = $pin_key;
    }

    /**
     * @param string $cardType
     */
    public function setCardType($cardType)
    {
        $this->card_type = $cardType;
    }
}