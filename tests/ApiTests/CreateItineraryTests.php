<?php

namespace Tests\ApiTests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

/**
 * Class CreateItineraryTests
 *
 * @package Tests
 * @author  Jaai Chandekar
 *
 * #########################################
 * Before running tests
 * Start php server : php -S localhost:8000
 * #########################################
 */
class CreateItineraryTests extends TestCase
{
    /** @var Client */
    protected $client;

    /**
     * Setup before each test
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost:8000',
        ]);

        parent::setUp();
    }

    /** create endpoint*/
    const ITINERARY_CREATE = '/App/Modules/Itinerary/Create.php';

    /**
     * Test itinerary with 1 boarding card
     *
     * @return void
     */
    public function testSingleBoardingCard(): void
    {
        $response = $this->client->request('POST', static::ITINERARY_CREATE, [
            'json' => [
                'cards'  => [
                    ['from'    => 'Barcelona',
                     'to'      => 'Gerona',
                     'type'    => 'Bus',
                     'subType' => 'Airport',
                    ],
                ],
                'output' => 'html',
            ],
        ]);

        $output = $response->getBody()->getContents();

        $this->assertEquals('<ul><li>Take the Airport bus from Barcelona to Gerona Airport. No seat assignment.</li><li>You have arrived at your final destination.</li></ul>',
            $output);
    }

    /**
     * Test itinerary with multiple shuffled boarding cards
     *
     * @return void
     */
    public function testItineraryWithMultipleBoardingCards(): void
    {
        $response = $this->client->request('POST', static::ITINERARY_CREATE, [
            'json' => [
                'cards'  => [
                    [
                        'to'        => 'New York JFK',
                        'from'      => 'Stockholm',
                        'type'      => 'flight',
                        'seat'      => '7B',
                        'number'    => 'SK22',
                        'gate'      => '22',
                        'isThrough' => true,
                    ],
                    [
                        'to'      => 'Gerona',
                        'from'    => 'Barcelona',
                        'type'    => 'Bus',
                        'subType' => 'Airport',
                    ],
                    [
                        'from'    => 'Gerona',
                        'to'      => 'Stockholm',
                        'type'    => 'flight',
                        'seat'    => '3A',
                        'number'  => 'SK455',
                        'gate'    => '45B',
                        'bagDrop' => '344',
                    ],
                    [
                        'from' => 'Madrid',
                        'to'   => 'Barcelona',
                        'type' => 'train',
                        'seat' => '45B',
                    ],
                ],
                'output' => 'html',
            ],
        ]);

        $output = $response->getBody()->getContents();

        $this->assertEquals(
            '<ul><li>Take train from Madrid to Barcelona. Sit in seat 45B.</li><li>Take the Airport bus from Barcelona to Gerona Airport. No seat assignment.</li><li>From Gerona Airport, take flight SK455 to Stockholm. Gate 45B, Seat 3A. Baggage drop at ticket counter 344.</li><li>From Stockholm Airport, take flight SK22 to New York JFK. Gate 22, Seat 7B. Baggage will be automatically transferred from your last leg.</li><li>You have arrived at your final destination.</li></ul>',
            $output);
    }

    /**
     * Test itinerary with multiple shuffled boarding cards and json output
     *
     * @return void
     */
    public function testItineraryWithMultipleBoardingCardsJsonOutput(): void
    {
        $response = $this->client->request('POST', static::ITINERARY_CREATE, [
            'json' => [
                'cards' => [
                    [
                        'to'        => 'New York JFK',
                        'from'      => 'Stockholm',
                        'type'      => 'flight',
                        'seat'      => '7B',
                        'number'    => 'SK22',
                        'gate'      => '22',
                        'isThrough' => true,
                    ],
                    [
                        'to'      => 'Gerona',
                        'from'    => 'Barcelona',
                        'type'    => 'Bus',
                        'subType' => 'Airport',
                    ],
                    [
                        'from'    => 'Gerona',
                        'to'      => 'Stockholm',
                        'type'    => 'flight',
                        'seat'    => '3A',
                        'number'  => 'SK455',
                        'gate'    => '45B',
                        'bagDrop' => '344',
                    ],
                    [
                        'from' => 'Madrid',
                        'to'   => 'Barcelona',
                        'type' => 'train',
                        'seat' => '45B',
                    ],
                ],
            ],
        ]);

        $output = $response->getBody()->getContents();

        $this->assertEquals(
            '["Take train from Madrid to Barcelona. Sit in seat 45B.","Take the Airport bus from Barcelona to Gerona Airport. No seat assignment.","From Gerona Airport, take flight SK455 to Stockholm. Gate 45B, Seat 3A. Baggage drop at ticket counter 344.","From Stockholm Airport, take flight SK22 to New York JFK. Gate 22, Seat 7B. Baggage will be automatically transferred from your last leg.","You have arrived at your final destination."]',
            $output);
    }

    /**
     * Test no 'cards' key in input pr empty body or empty cards value
     *
     * @return void
     */
    public function testEmptyCardsInInput(): void
    {
        // wrong key
        $response = $this->client->request('POST', static::ITINERARY_CREATE, [
            'json' => [
                'passes' => [
                    ['from'    => 'Barcelona',
                     'to'      => 'Gerona',
                     'type'    => 'Bus',
                     'subType' => 'Airport',
                    ],
                ],
            ],
        ]);

        $output = $response->getBody()->getContents();

        $this->assertEquals('{"status":"400","title":"Bad Request","detail":"No boarding cards in input !","type":"Client Error"}',
            $output);

        // empty body
        $response = $this->client->request('POST', static::ITINERARY_CREATE, [
            'json' => [],
        ]);

        $output = $response->getBody()->getContents();

        $this->assertEquals('{"status":"400","title":"Bad Request","detail":"No boarding cards in input !","type":"Client Error"}',
            $output);


        // cards => []
        $response = $this->client->request('POST', static::ITINERARY_CREATE, [
            'json' => ['cards' => []],
        ]);

        $output = $response->getBody()->getContents();

        $this->assertEquals('{"status":"400","title":"Bad Request","detail":"No boarding cards in input !","type":"Client Error"}',
            $output);
    }

    /**
     * Test missing any of required card keys - to, from, type
     *
     * @return void
     */
    public function testCardsMissingRequiredParams(): void
    {
        $response = $this->client->request('POST', static::ITINERARY_CREATE, [
            'json' => [
                'cards' => [
                    ['from'    => 'Barcelona',
                     'subType' => 'Airport',
                    ],
                ],
            ],
        ]);

        $output = $response->getBody()->getContents();

        $this->assertEquals('{"status":"400","title":"Bad Request","detail":"type, from & to attributes of boarding card are needed!","type":"Client Error"}',
            $output);
    }


    /**
     * Test broken path
     *
     * @return void
     */
    public function testBrokenPath(): void
    {
        $response = $this->client->request('POST', static::ITINERARY_CREATE, [
            'json' => [
                'cards' => [
                    [
                        'from'      => 'Seattle',
                        'to'        => 'New York JFK',
                        'type'      => 'flight',
                        'seat'      => '7B',
                        'number'    => 'SK22',
                        'gate'      => '22',
                        'isThrough' => true,
                    ],
                    [
                        'from'    => 'Barcelona',
                        'to'      => 'Gerona',
                        'type'    => 'Bus',
                        'subType' => 'Airport',
                    ],
                    [
                        'from'    => 'Gerona',
                        'to'      => 'Stockholm',
                        'type'    => 'flight',
                        'seat'    => '3A',
                        'number'  => 'SK455',
                        'gate'    => '45B',
                        'bagDrop' => '344',
                    ],
                    [
                        'from' => 'Madrid',
                        'to'   => 'Barcelona',
                        'type' => 'train',
                        'seat' => '45B',
                    ],
                ],
            ],
        ]);

        $output = $response->getBody()->getContents();

        $this->assertEquals('{"status":"400","title":"Bad Request","detail":"Not a liner sequence or Broken sequence !","type":"Client Error"}',
            $output);
    }

    /**
     * Test loops in path of type A-B-C-D-C
     *
     * @return void
     */
    public function testLoopsInPath1(): void
    {
        $response = $this->client->request('POST', static::ITINERARY_CREATE, [
            'json' => [
                'cards' => [
                    [
                        'to'        => 'Gerona',
                        'from'      => 'Stockholm',
                        'type'      => 'flight',
                        'seat'      => '7B',
                        'number'    => 'SK22',
                        'gate'      => '22',
                        'isThrough' => true,
                    ],
                    [
                        'to'      => 'Gerona',
                        'from'    => 'Barcelona',
                        'type'    => 'Bus',
                        'subType' => 'Airport',
                    ],
                    [
                        'from'    => 'Gerona',
                        'to'      => 'Stockholm',
                        'type'    => 'flight',
                        'seat'    => '3A',
                        'number'  => 'SK455',
                        'gate'    => '45B',
                        'bagDrop' => '344',
                    ],
                    [
                        'from' => 'Madrid',
                        'to'   => 'Barcelona',
                        'type' => 'train',
                        'seat' => '45B',
                    ],
                ],
            ],
        ]);

        $output = $response->getBody()->getContents();

        $this->assertEquals('{"status":"400","title":"Bad Request","detail":"Not a liner sequence or Broken sequence !","type":"Client Error"}',
            $output);
    }

    /**
     * Test loops in path of type A-B-C-D-B
     *
     * @return void
     */
    public function testLoopInPath2(): void
    {
        $response = $this->client->request('POST', static::ITINERARY_CREATE, [
            'json' => [
                'cards' => [
                    [
                        'to'        => 'Barcelona',
                        'from'      => 'Stockholm',
                        'type'      => 'flight',
                        'seat'      => '7B',
                        'number'    => 'SK22',
                        'gate'      => '22',
                        'isThrough' => true,
                    ],
                    [
                        'to'      => 'Gerona',
                        'from'    => 'Barcelona',
                        'type'    => 'Bus',
                        'subType' => 'Airport',
                    ],
                    [
                        'from'    => 'Gerona',
                        'to'      => 'Stockholm',
                        'type'    => 'flight',
                        'seat'    => '3A',
                        'number'  => 'SK455',
                        'gate'    => '45B',
                        'bagDrop' => '344',
                    ],
                    [
                        'from' => 'Madrid',
                        'to'   => 'Barcelona',
                        'type' => 'train',
                        'seat' => '45B',
                    ],
                ],
            ],
        ]);

        $output = $response->getBody()->getContents();

        $this->assertEquals('{"status":"400","title":"Bad Request","detail":"Not a liner sequence or Broken sequence !","type":"Client Error"}',
            $output);
    }

    /**
     * Test loops in path of type A-B-C-D-A
     *
     * @return void
     */
    public function testRoundTripPath(): void
    {
        $response = $this->client->request('POST', static::ITINERARY_CREATE, [
            'json' => [
                'cards' => [
                    [
                        'to'        => 'Madrid',
                        'from'      => 'Stockholm',
                        'type'      => 'flight',
                        'seat'      => '7B',
                        'number'    => 'SK22',
                        'gate'      => '22',
                        'isThrough' => true,
                    ],
                    [
                        'to'      => 'Gerona',
                        'from'    => 'Barcelona',
                        'type'    => 'Bus',
                        'subType' => 'Airport',
                    ],
                    [
                        'from'    => 'Gerona',
                        'to'      => 'Stockholm',
                        'type'    => 'flight',
                        'seat'    => '3A',
                        'number'  => 'SK455',
                        'gate'    => '45B',
                        'bagDrop' => '344',
                    ],
                    [
                        'from' => 'Madrid',
                        'to'   => 'Barcelona',
                        'type' => 'train',
                        'seat' => '45B',
                    ],
                ],
            ],
        ]);

        $output = $response->getBody()->getContents();

        $this->assertEquals('{"status":"400","title":"Bad Request","detail":"Not a liner sequence or Broken sequence !","type":"Client Error"}',
            $output);
    }

    /**
     * Test non implemented boarding card type
     *
     * @return void
     */
    public function testUnknownBoardingCardType(): void
    {
        $response = $this->client->request('POST', static::ITINERARY_CREATE, [
            'json' => [
                'cards' => [
                    ['from' => 'Barcelona',
                     'to'   => 'Gerona',
                     'type' => 'Cruise',
                    ],
                ],
            ],
        ]);

        $output = $response->getBody()->getContents();

        $this->assertEquals('{"status":"500","title":"Internal Server Error","detail":"Boarding card of type cruise is not implemented.","type":"Server Error"}',
            $output);
    }
}