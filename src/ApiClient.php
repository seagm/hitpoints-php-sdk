<?php

namespace SeaGM\HitPoints;

use SeaGM\HitPoints\Contract\ClientAbstract;
use SeaGM\HitPoints\Resource\FetchPinRequest;
use SeaGM\HitPoints\Resource\ItemsRequest;
use SeaGM\HitPoints\Resource\OrderQueryRequest;
use SeaGM\HitPoints\Resource\PayTokenRequest;
use SeaGM\HitPoints\Resource\PinPayRequest;
use SeaGM\HitPoints\Resource\PinQueryRequest;
use SeaGM\HitPoints\Resource\QueryReloadRequest;

/**
 * Class ApiClient
 * @package SeaGM\HitPoints
 */
class ApiClient extends ClientAbstract
{

    /**
     * Api get authorized HitPoints items
     *
     * @return ItemsRequest
     */
    public function newItemsRequest()
    {
        return new ItemsRequest();
    }

    /**
     * @return PayTokenRequest
     */
    public function newPayTokenRequest()
    {
        return new PayTokenRequest();
    }

    /**
     * @return QueryReloadRequest
     */
    public function newQueryReloadRequest()
    {
        return new QueryReloadRequest();
    }

    /**
     * @return PinQueryRequest
     */
    public function newPinQueryRequest()
    {
        return new PinQueryRequest();
    }

    /**
     * @return PinPayRequest
     */
    public function newPinPayRequest()
    {
        return new PinPayRequest();
    }

    /**
     * @return FetchPinRequest
     */
    public function newFetchPinRequest()
    {
        return new FetchPinRequest();
    }

    /**
     * @return OrderQueryRequest
     */
    public function newOrderQueryRequest()
    {
        return new OrderQueryRequest();
    }

}
