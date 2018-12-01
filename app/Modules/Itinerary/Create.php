<?php
require "../../../vendor/autoload.php";

use App\Modules\Itinerary\Request\CreateItineraryRequest;
use App\Core\Exceptions\BadRequestException;
use App\Core\Exceptions\ServerException;

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    header("HTTP/1.1 403 Forbidden");
    $exception = new BadRequestException('Forbidden', 'HTTP/1.1 403 Forbidden', '403');
    echo json_encode($exception->toArray());
    exit();
}

// read input
$data = json_decode(file_get_contents('php://input'), true);

try {
    $request  = new CreateItineraryRequest($data);
    $response = $request->load()
                        ->process();
} catch (BadRequestException | ServerException $exception) {
    $response = json_encode($exception->toArray());
} catch (Throwable $exception) {
    $response = [$exception->getMessage(), $exception->getTrace()];
    $response = json_encode($response);
}

echo $response;


