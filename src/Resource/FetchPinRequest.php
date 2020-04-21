<?php

namespace SeaGM\HitPoints\Resource;

use SeaGM\HitPoints\Contract\RequestAbstract;
use SeaGM\HitPoints\Lib\RequestMethod;

/**
 * Class FetchPinRequest
 * @package SeaGM\HitPoints\Resource
 */
class FetchPinRequest extends RequestAbstract
{
    protected $apiUri = '/v1/pin/fetch';

    protected $requestMethod = RequestMethod::POST;

    /**
     * Reseller's fetch pin record ID, it's should be unique in reseller's system
     * It's required and max length is 50
     * @var string
     */
    protected $out_trade_id;

    /**
     * HitPoints product ID
     * Reseller can get the product list through ItemsRequest api
     * It's required
     * @var int
     */
    protected $product_id;

    /**
     * The quantity of pins for the request, the quantity should be lower than the max allowed number for each pin
     * Refer to the parameter [max_fetch_quantity] in product list in ItemsRequest api
     * It's required
     * @var int
     */
    protected $quantity;

    /**
     * @param string $out_trade_id
     */
    public function setOutTradeId($out_trade_id)
    {
        $this->out_trade_id = $out_trade_id;
    }

    /**
     * @param int $product_id
     */
    public function setProductId($product_id)
    {
        $this->product_id = $product_id;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }


}