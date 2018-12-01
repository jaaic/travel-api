## Travel Api

A REST API to get travel planned.
This is a pure PHP api not using any framework.

## Pre-requisities
1. PHP >= 7.1.0
2. Install composer https://getcomposer.org


## How to setup?
1. Clone the code to your machine

    ```
    git clone git@github.com:jaaic/travel-api.git
    ```
    
2. Install dependencies

    ```
    composer install
    
    ```
    
3. Load class map

    ```
    composer dumpautoload -o
    
    ```  

4. Start PHP built-in web server 
   http://php.net/manual/en/features.commandline.webserver.php
    ```
    php -S localhost:8000
    ```
 
## Invoke api
Each boarding card has mandatory fields - 'from', 'to', 'type'

The api to sort list of different boarding cards and create an itinerary takes a json input and can be invoked as -
 
```      
curl -X POST \
  http://localhost:8000/App/Modules/Itinerary/Create.php \
  -H 'Content-Type: application/json' \
  -d '{
	"cards": [
		{
			"from": "Stockholm",
	        "to": "New York JFK",
        	"type": "Flight",
        	"number": "SK22",
        	"gate": "22",
        	"seat": "7B",
        	"isThrough": true
		},
		{
			"from": "Madrid",
	        "to": "Barcelona",
        	"type": "train",
        	"number": "7A",
        	"seat": "45B"
		},
		{
			"from": "Gerona",
	        "to": "Stockholm",
        	"type": "Flight",
        	"number": "SK22",
        	"gate": "45B"
		},
		{
			"from": "Barcelona",
	        "to": "Gerona",
        	"type": "bus"
		}
    ],
    "output": "html"
}
'
```

## Output

The output of the create itinerary api is a string with list of transitions to get from trip origin to
final destination.
Currently api supports 2 types of output strings -
1. json
2. html

with default to json. The output type can be specified in request under key "output".
To support other types of outputs, new method implementations can be added to CreateItineraryResponse class.

The HTML output of above input is -
```
<ul>
<li>Take train 7A from Madrid to Barcelona. Sit in seat 45B.</li>
<li>Take the bus from Barcelona to Gerona . No seat assignment.</li>
<li>From Gerona Airport, take flight SK22 to Stockholm. Gate 45B.</li>
<li>From Stockholm Airport, take flight SK22 to New York JFK. Gate 22, Seat 7B. Baggage will be automatically transferred from your last leg.</li>
<li>You have arrived at your final destination.</li>
</ul>
```

In case of loops, broken paths, branches in the path, the api returns a json formatted string 
```

{"status":"400","title":"Bad Request","detail":"Not a liner sequence or Broken sequence !","type":"Client Error"}

```

Currently the create itinerary api supports boarding cards for bus, train and flight. On receiving a boarding card of
unimplemented type in input (example 'Cruise'), the api responds with-

```
{"status":"500","title":"Internal Server Error","detail":"Boarding card of type cruise is not implemented.","type":"Server Error"}
```

If any of the required keys are missing, the api responds with client error. Examples -
```
{"status":"400","title":"Bad Request","detail":"No boarding cards in input !","type":"Client Error"}

```

```
{"status":"400","title":"Bad Request","detail":"type, from & to attributes of boarding card are needed!","type":"Client Error"}

```

## Assumptions
1. There are no loops in the path. The output in this case would be like -
    ```
    {"status":"400","title":"Bad Request","detail":"Not a liner sequence or Broken sequence !","type":"Client Error"}
    ``` 

2. It is a linear path with no branches. The output in this case would be like -
    ```
    {"status":"400","title":"Bad Request","detail":"Not a liner sequence or Broken sequence !","type":"Client Error"}
    ``` 

## Algorithm
- Get the first and last cards
    1. Iterate over the list of cards. 
       During the iteration, populate arrays of probable trip origins, final destinations, hash map of source => card.
    
       i) Add the first card to both origins and destinations arrays.
       
       ii) For each next card, check -
       
       - Is Card destination already in current list of probable origins. 
         If it is, then it means some path is already starting at this place. So do not add this place to the array of
         final destinations and remove the place from origins as well.
         Else, add it to final destinations list
       
       - Is the Card origin already in current list of probable final destinations.
         If it is, then it means some path is already ending at this place. So do not add this place to the array of
         origins and remove the place from final destinations as well.
         Else, add it to probable origins list
          
    2. After the iteration, for a linear path, we should be left with only 1 card in origins and final destinations arrays.
       If it is not so, means path has loops, branches or is broken.

- Find path from origin to destination
    
    1. Get the source of first card from origins array as the trip start. The trip ends at the destination of card in 
       destinations array.    
    2. Until we reach the destination, keep adding cards to output array     

## Algorithm
CardSortService 2 separate for loops - 
1) To get the origin and destination
2) To construct the path from origin to destination

Hence the time complexity is O(n) + O(n) = 2 O(n) which can be amortized as O(n)

## To add new boarding card types
Create a new boarding card class extending from App\Core\Models\BoardingCards. Add additional properties and 
implement setDescription method as per required.
   
## To add new boarding card sub types
Airport bus is considered to be subtype of bus.
Similarly, if there are more subtypes of existing transport modes, add 'subtype' field to the boarding card json
and modify the setDescription method to include this subtype.
   
## To add new output formats
Create a new method in CreateItineraryResponse class to handle the new format specified under "output" key in 
request json.
   
## Running tests
Run tests using phpunit -
```
bin/phpunit tests/ApiTests/CreateItineraryTests.php

```
