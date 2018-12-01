<?php

namespace App\Core\Contracts;


/**
 * Interface Response
 *
 * @package App\Core\Contracts
 * @author  Jaai Chandekar
 *
 */
interface Response
{
    /**
     * Function to be implemented by specific responses.
     *
     * @param array  $response
     * @param string $outputTypeType
     *
     * @return string
     */
    function format(array $response, string $outputTypeType): string;
}