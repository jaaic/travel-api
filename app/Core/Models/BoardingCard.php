<?php

namespace App\Core\Models;

use App\Core\Exceptions\BadRequestException;

/**
 * Class BoardingCard
 *
 * @package App\Core\Models
 * @author  Jaai Chandekar
 */
abstract class BoardingCard
{
    /** @var string */
    protected $number;

    /** @var string */
    protected $seat;

    /** @var string */
    protected $type;

    /** @var string */
    protected $description;

    /** @var string */
    protected $from;

    /** @var string */
    protected $to;

    /**
     * BoardingCard constructor.
     *
     * @param string $from
     * @param string $to
     * @param string $type
     * @param string $number
     * @param string $seat
     */
    protected function __construct(string $from, string $to, string $type, string $number, string $seat)
    {
        $this->from   = $from;
        $this->to     = $to;
        $this->type   = $type;
        $this->number = $number;
        $this->seat   = $seat;
    }


    /**
     * Get Source
     *
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * Get Destination
     *
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get Number
     *
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number ?? '';
    }

    /**
     * Get Seat
     *
     * @return string
     */
    public function getSeat(): string
    {
        return $this->seat ?? '';
    }

    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    /**
     * Validate attributes of BoardingCard
     *
     * @param string $type
     * @param string $from
     * @param string $to
     *
     * @throws \App\Core\Exceptions\BadRequestException
     */
    public static function validate(string $type, string $from, string $to)
    {
        if (empty($type) || empty($from) || empty($to)) {
            throw new BadRequestException('type, from & to attributes of boarding card are needed!');
        }
    }

    /**
     * Set description
     *
     * @return void
     */
    abstract function setDescription(): void;

}