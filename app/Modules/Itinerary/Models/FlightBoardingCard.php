<?php

namespace App\Modules\Itinerary\Models;

use \App\Core\Models\BoardingCard;

/**
 * Class FlightBoardingCard
 *
 * @package App\Modules\Itinerary\Models
 * @author  Jaai Chandekar
 */
class FlightBoardingCard extends BoardingCard
{
    /** @var string */
    protected $gate;

    /** @var string */
    protected $bagDrop;

    /** @var bool */
    protected $isThrough;

    /**
     * Flight constructor.
     *
     * @param string $from
     * @param string $to
     * @param string $type
     * @param string $number
     * @param string $seat
     * @param string $gate
     * @param string $bagDrop
     * @param bool   $isThrough
     */
    public function __construct(string $from, string $to, string $type, string $number, string $seat = '',
                                string $gate = '', string $bagDrop = '', bool $isThrough = false)
    {
        $this->gate      = $gate;
        $this->bagDrop   = $bagDrop;
        $this->isThrough = $isThrough;

        parent::__construct($from, $to, $type, $number, $seat);
    }

    /**
     * Set description of transport
     *
     * @return void
     */
    public function setDescription(): void
    {
        $from      = $this->getFrom();
        $to        = $this->getTo();
        $type      = $this->getType();
        $number    = $this->getNumber();
        $gate      = $this->getGate();
        $seat      = $this->getSeat();
        $isThrough = $this->getIsThrough();
        $bagDrop   = $this->getBagDrop();

        $this->description = "From $from Airport, take $type $number to $to.";

        if (!empty($gate)) {
            $this->description .= " Gate $gate";
        }

        if (!empty($this->getSeat())) {
            $this->description .= ", Seat $seat.";
        } else {
            $this->description .= '.';
        }

        if ($isThrough) {
            $this->description .= ' Baggage will be automatically transferred from your last leg.';
        } elseif (!empty($bagDrop)) {
            $this->description .= " Baggage drop at ticket counter $bagDrop.";
        }
    }

    /**
     * Get gate
     *
     * @return string
     */
    public function getGate(): string
    {
        return $this->gate;
    }

    /**
     * Get isThrough luggage flag
     *
     * @return bool
     */
    public function getIsThrough(): bool
    {
        return $this->isThrough;
    }

    /**
     * Get bag drop
     *
     * @return string
     */
    public function getBagDrop(): string
    {
        return $this->bagDrop;
    }
}