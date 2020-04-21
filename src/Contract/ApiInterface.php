<?php

namespace SeaGM\HitPoints\Contract;

/**
 * Interface ApiInterface
 * @package SeaGM\HitPoints\Contract
 */
interface ApiInterface
{
    /**
     * @param ParameterInterface $requestData
     */
    public function request(ParameterInterface $requestData);
}