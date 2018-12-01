<?php

namespace App\Core\Factories;

use App\Core\Exceptions\ServerException;
use App\Core\Models\BoardingCard;
use App\Modules\Itinerary\Models\BusBoardingCard;
use App\Modules\Itinerary\Models\FlightBoardingCard;
use App\Modules\Itinerary\Models\TrainBoardingCard;

/**
 * Class BoardingCardFactory. Gets instance of boarding cards as per 'type'
 *
 * @package App\Core\Factories
 * @author  Jaai Chandekar
 */
class BoardingCardFactory
{
    /**
     * Get Transport by type
     *
     * @param array $attributes
     *
     * @return BoardingCard
     * @throws \App\Core\Exceptions\ServerException
     * @throws \App\Core\Exceptions\BadRequestException
     */
    public static function getCardByMode(array $attributes): BoardingCard
    {
        $type = strtolower($attributes['type'] ?? '');
        $from = $attributes['from'] ?? '';
        $to   = $attributes['to'] ?? '';

        BoardingCard::validate($type, $from, $to);

        $number    = $attributes['number'] ?? '';
        $seat      = $attributes['seat'] ?? '';
        $gate      = $attributes['gate'] ?? '';
        $bagDrop   = $attributes['bagDrop'] ?? '';
        $isThrough = $attributes['isThrough'] ?? false;
        $subType   = $attributes['subType'] ?? '';

        switch ($type) {
            case 'train':
                return new TrainBoardingCard($from, $to, $type, $number, $seat);

            case 'flight':
                return new FlightBoardingCard($from, $to, $type, $number, $seat, $gate, $bagDrop, $isThrough);

            case 'bus':
                return new BusBoardingCard($from, $to, $type, $number, $seat, $subType);

            default:
                throw new ServerException("Boarding card of type {$type} is not implemented.");
        }
    }
}