<?php

namespace SeaGM\HitPoints\Resource;

use SeaGM\HitPoints\Contract\RequestAbstract;
use SeaGM\HitPoints\Lib\RequestMethod;

/**
 * Class OrderQueryRequest
 * @package SeaGM\HitPoints\Resource
 */
class OrderQueryRequest extends RequestAbstract
{
    protected $apiUri = '/v1/order/query';

    protected $requestMethod = RequestMethod::GET;

    /**
     * Reseller's fetch pin record ID, it's should be unique in reseller's system
     * It's required and max length is 50
     * @var string
     */
    protected $out_trade_id;

    /**
     * @param string $out_trade_id
     */
    public function setOutTradeId($out_trade_id)
    {
        $this->out_trade_id = $out_trade_id;
    }
}