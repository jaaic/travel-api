<?php

namespace App\Modules\Itinerary\Models;

use App\Core\Models\BoardingCard;

/**
 * Class BusBoardingCard
 *
 * @package App\Modules\Itinerary\Models
 * @author  Jaai Chandekar
 */
class BusBoardingCard extends BoardingCard
{
    /** @var string */
    protected $subType;

    /**
     * Bus constructor.
     *
     * @param string $from
     * @param string $to
     * @param string $type
     * @param string $number
     * @param string $seat
     * @param string $subType
     */
    public function __construct(string $from, string $to, string $type, string $number, string $seat = '', string $subType = '')
    {
        $this->subType = $subType;

        parent::__construct($from, $to, $type, $number, $seat);
    }

    /**
     * Set description of transport
     *
     * @return void
     */
    public function setDescription(): void
    {
        $from    = $this->getFrom();
        $to      = $this->getTo();
        $type    = $this->getType();
        $subType = $this->getSubType();
        $seat    = $this->getSeat();
        $number  = $this->getNumber();

        $sub = empty($subType) ? '' : "$subType ";
        $num = empty($number) ? '' : " $number";


        $this->description = 'Take the ' . $sub . $type . $number . " from $from to $to $subType.";


        if (empty($seat)) {
            $this->description .= ' No seat assignment.';
        } else {
            $this->description .= " Sit in seat $seat.";
        }
    }

    /**
     * Get sub type (eg: Airport bus, tourist bus, public transport bus etc)
     *
     * @return string
     */
    public function getSubType(): string
    {
        return $this->subType ?? '';
    }
}