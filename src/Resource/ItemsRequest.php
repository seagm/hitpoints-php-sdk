<?php

namespace SeaGM\HitPoints\Resource;

use SeaGM\HitPoints\Contract\RequestAbstract;
use SeaGM\HitPoints\Lib\RequestMethod;

/**
 * Class ItemsRequest
 * @package SeaGM\HitPoints\Resource
 */
class ItemsRequest extends RequestAbstract
{

    protected $apiUri = '/v1/items';

    protected $requestMethod = RequestMethod::GET;

    /**
     * Currency code
     *
     * @var string
     */
    protected $currency;

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

}