<?php

namespace App\Modules\Itinerary\Request;

use App\Core\Contracts\Request;
use App\Core\Exceptions\BadRequestException;
use App\Core\Factories\BoardingCardFactory;
use App\Modules\Itinerary\Response\CreateItineraryResponse;
use App\Modules\Itinerary\Service\CardSortService;

/**
 * Class CreateItineraryRequest
 *
 * @author Jaai Chandekar
 */
class CreateItineraryRequest implements Request
{
    /** @var array */
    protected $input;

    /** @var array */
    protected $boardingPasses;

    /** @var string */
    protected $output;

    /**
     * CreateItineraryRequest constructor.
     *
     * @param array $input
     */
    public function __construct(array $input)
    {
        $this->input          = $input;
        $this->boardingPasses = [];
    }

    /**
     * Load boarding passes
     *
     * @return Request
     *
     * @throws \App\Core\Exceptions\BadRequestException
     * @throws \App\Core\Exceptions\ServerException
     */
    public function load(): Request
    {
        $inputCards = $this->input['cards'] ?? [];
        if (empty($inputCards)) {
            throw new BadRequestException('No boarding cards in input !');
        }

        foreach ($inputCards as $card) {
            $boardingCard = BoardingCardFactory::getCardByMode($card);
            $boardingCard->setDescription();
            $this->boardingPasses[] = $boardingCard;
        }

        $this->output = $this->input['output'] ?? 'json';

        return $this;
    }

    /**
     * Sort Cards and create itinerary description
     *
     * @return string
     * @throws \App\Core\Exceptions\BadRequestException
     */
    public function process(): string
    {
        $cardSortService = new CardSortService();
        $orderedCards    = $cardSortService->makeItinerary($this->boardingPasses);
        $response        = new CreateItineraryResponse();

        return $response->format($orderedCards, $this->output);
    }
}