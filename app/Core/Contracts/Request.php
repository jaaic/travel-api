<?php

namespace App\Core\Contracts;

/**
 * Interface Request
 *
 * @package App\Core\Contracts
 * @author  Jaai Chandekar
 *
 */
interface Request
{
    /**
     * Load the request params
     *
     * @return Request
     */
    public function load(): Request;

    /**
     * Process request and return response
     *
     * @return string
     */
    public function process(): string;
}