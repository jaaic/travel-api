<?php

namespace App\Core\Exceptions;

/**
 * Class ServerException
 *
 * @package App\Core\Exceptions
 * @author  Jaai Chandekar
 */
class ServerException extends BaseException
{
    /** @var string */
    protected $status = '500';

    /** @var string */
    protected $title = 'Internal Server Error';

    /** @var string */
    protected $type = 'Server Error';

    /** @var string */
    protected $detail = '';
}