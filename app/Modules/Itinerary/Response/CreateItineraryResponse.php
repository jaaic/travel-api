<?php

namespace App\Modules\Itinerary\Response;

use App\Core\Contracts\Response;
use App\Core\Models\BoardingCard;

/***
 * Class CreateItineraryResponse
 *
 * @package App\Modules\Itinerary\Response
 * @author  Jaai Chandekar
 */
class CreateItineraryResponse implements Response
{
    /**
     * Format output as per request
     *
     * @param array  $boardingCards
     * @param string $outputType
     *
     * @return string
     */
    public function format(array $boardingCards, string $outputType): string
    {
        switch (strtolower($outputType)) {
            case 'html' :
                return $this->formatToHtml($boardingCards);

            case 'json' :
                return $this->formatToJson($boardingCards);

            default :
                echo "Output format {$outputType} is not implemented. Default to json ..";

                return $this->formatToJson($boardingCards);
        }
    }

    /**
     * Format response to html string
     *
     * @param array
     *
     * @return string
     */
    public function formatToHtml(array $boardingCards): string
    {
        $description = '<ul>';
        foreach ($boardingCards as $boardingCard) {
            /**@var BoardingCard $boardingCard */
            $description .= '<li>' . $boardingCard->getDescription() . '</li>';
        }

        $description .= '<li>You have arrived at your final destination.</li>';
        $description .= '</ul>';

        return $description;
    }

    /**
     * Format response to json encoded string
     *
     * @param array
     *
     * @return string
     */
    public function formatToJson(array $boardingCards): string
    {
        $description = [];
        foreach ($boardingCards as $boardingCard) {
            /**@var BoardingCard $boardingCard */
            $description[] = $boardingCard->getDescription();
        }

        $description[] = 'You have arrived at your final destination.';

        return json_encode($description);
    }
}