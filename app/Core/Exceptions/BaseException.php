<?php

namespace App\Core\Exceptions;

use Exception;

/**
 * Class BaseException
 *
 * @package App\Core\Exceptions
 * @author  Jaai Chandekar
 */
abstract class BaseException extends Exception
{
    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $detail;

    /**
     * @var string
     */
    protected $trace;

    /** @var string */
    protected $type;


    /**
     * Get the status
     *
     * @return int
     */
    public function getStatus(): int
    {
        return (int)$this->status;
    }

    /**
     * BaseException constructor.
     *
     * @param string $detail
     * @param string $title
     * @param string $status
     * @param string $trace
     */
    public function __construct($detail, $title = '', $status = '', $trace = '')
    {
        $this->detail = $detail ?: $this->detail;
        $this->title  = $title ?: $this->title;
        $this->status = $status ?: $this->status;
        $this->trace  = $trace ?: $this->trace;

        parent::__construct($this->detail);
    }

    /**
     * Return the Exception as an array
     *
     * @return array
     */
    public function toArray()
    {
        return array_filter([
            'status' => $this->status,
            'title'  => $this->title,
            'detail' => $this->detail,
            'type'   => !empty($this->type) ? $this->type : 'https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
            'trace'  => $this->trace,
        ]);
    }
}