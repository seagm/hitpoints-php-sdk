<?php

namespace SeaGM\HitPoints\Resource;

use SeaGM\HitPoints\Contract\RequestAbstract;
use SeaGM\HitPoints\Lib\RequestMethod;

/**
 * Class PinQueryRequest
 * @package SeaGM\HitPoints\Resource
 */
class PinQueryRequest extends RequestAbstract
{
    protected $apiUri = '/v1/pin/query';

    protected $requestMethod = RequestMethod::GET;

    /**
     * The HitPoints Pin SN
     *
     * @var string
     */
    protected $pin_sn;

    /**
     * @param string $pin_sn
     */
    public function setPinSn($pin_sn)
    {
        $this->pin_sn = $pin_sn;
    }

}