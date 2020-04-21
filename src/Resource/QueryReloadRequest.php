<?php

namespace SeaGM\HitPoints\Resource;

use SeaGM\HitPoints\Contract\RequestAbstract;
use SeaGM\HitPoints\Lib\RequestMethod;

/**
 * Class QueryReloadRequest
 * @package SeaGM\HitPoints\Resource
 */
class QueryReloadRequest extends RequestAbstract
{

    protected $requestMethod = RequestMethod::GET;

    protected $apiUri = '/v1/reload/query';

    /**
     * Query by merchant reference id
     *
     * @var string
     */
    protected $reference_id;

    /**
     * Query by HitPoints transaction id
     *
     * @var string
     */
    protected $transaction_id;

    /**
     * Query by HitPoints Pin SN
     *
     * @var string
     */
    protected $pin_sn;

    /**
     * @param string $reference_id
     */
    public function setReferenceId($reference_id)
    {
        $this->reference_id = $reference_id;
    }

    /**
     * @param string $transaction_id
     */
    public function setTransactionId($transaction_id)
    {
        $this->transaction_id = $transaction_id;
    }

    /**
     * @param string $pin_sn
     */
    public function setPinSn($pin_sn)
    {
        $this->pin_sn = $pin_sn;
    }

}