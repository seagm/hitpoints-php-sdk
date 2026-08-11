<?php

namespace SeaGM\HitPoints\Resource;

use SeaGM\HitPoints\Contract\RequestAbstract;
use SeaGM\HitPoints\Lib\RequestMethod;

/**
 * Class PinRevokeRequest
 * @package SeaGM\HitPoints\Resource
 */
class PinRevokeRequest extends RequestAbstract
{
    protected $apiUri = '/v1/pin/revoke';

    protected $requestMethod = RequestMethod::POST;

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
