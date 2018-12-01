<?php

namespace App\Modules\Itinerary\Service;

use App\Core\Exceptions\BadRequestException;
use App\Core\Models\BoardingCard;

/**
 * Class CardSortService
 *
 * @author Jaai Chandekar
 */
class CardSortService
{
    /**
     * Sort boarding cards to make a itinerary
     *
     * @param array $cards
     *
     * @return array
     * @throws \App\Core\Exceptions\BadRequestException
     */
    public function makeItinerary(array $cards)
    {
        $origins      = []; // probable itinerary origins
        $destinations = []; // probable itinerary final destinations
        $from         = []; // list of all sources mapped to cardIds

        foreach ($cards as $card) {
            /** @var \App\Core\Models\BoardingCard $card */
            $cardSource      = $card->getFrom();
            $cardDestination = $card->getTo();

            // hash map of source => card
            // for easy O(1) fetch while constructing the path
            $from[$cardSource] = $card;

            // check if any path ends at current card source in the probable final destinations array
            // if it does, then it is not a probable itinerary origin
            if (in_array($cardSource, array_keys($destinations))) {
                unset($destinations[$cardSource]);

            } else {
                // if card source is not a destination of any cards seen till now
                // then it could be the trip origin
                // so add it to list of probable origins
                $origins[$cardSource] = $card;
            }

            // check if any path starts at current card destination
            // if it does, then it is not a probable itinerary final destination
            if (in_array($cardDestination, array_keys($origins))) {
                unset($origins[$cardDestination]);

            } else {
                // if card destination is not origin of any other card seen till now
                // then it could be final destination of the itinerary
                // so add it to list of probable destinations
                $destinations[$cardDestination] = $card;
            }
        }

        // at the end of above for loop, we should be left with 1 card in origins array which is trip start
        // and 1 card in final destinations array which will be the trip end
        // if there are cycles or sequence is broken then this won't be true
        if (count($origins) != 1 || count($destinations) != 1) {
            throw new BadRequestException('Not a liner sequence or Broken sequence !');
        }

        $orderedCards = [];

        /** @var BoardingCard $card */
        $card = array_values($origins)[0];

        /** @var BoardingCard $finalDestination */
        $finalDestination = array_values($destinations)[0];

        $orderedCards[] = $card;

        // starting from itinerary origin until we reach end, keep adding cards in a array
        while ($card->getTo() != $finalDestination->getTo()) {
            $nextCard       = $from[$card->getTo()];
            $orderedCards[] = $nextCard;
            $card           = $nextCard;
        }

        // finally check if ordered cards are equal to input cards
        // this check is useful to eliminate  A-B-C-D-B
        if (count($orderedCards) != count($cards)) {
            throw new BadRequestException('Not a liner sequence or Broken sequence !');
        }

        return $orderedCards;
    }
}