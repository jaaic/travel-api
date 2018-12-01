<?php

namespace App\Modules\Itinerary\Models;

use App\Core\Models\BoardingCard;

/**
 * Class TrainBoardingCard
 *
 * @package App\Modules\Itinerary\Models
 * @author  Jaai Chandekar
 */
class TrainBoardingCard extends BoardingCard
{
    /**
     * TrainBoardingCard constructor.
     *
     * @param string $from
     * @param string $to
     * @param string $type
     * @param string $number
     * @param string $seat
     */
    public function __construct(string $from, string $to, string $type, string $number, string $seat = '')
    {
        parent::__construct($from, $to, $type, $number, $seat);
    }

    /**
     * Set description of transport
     *
     * @return void
     */
    public function setDescription(): void
    {
        $from   = $this->getFrom();
        $to     = $this->getTo();
        $type   = $this->getType();
        $seat   = $this->getSeat();
        $number = $this->getNumber();

        $num = empty($number) ? '' : " $number";

        $this->description = "Take $type$num from $from to $to.";

        if (!empty($seat)) {
            $this->description .= " Sit in seat $seat.";
        }
    }
}