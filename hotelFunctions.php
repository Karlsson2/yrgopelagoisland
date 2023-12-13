<?php

declare(strict_types=1);
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use GuzzleHttp\Client;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

/* 
Here's something to start your career as a hotel manager.

One function to connect to the database you want (it will return a PDO object which you then can use.)
    For instance: $db = connect('hotel.db');
                  $db->prepare("SELECT * FROM bookings");
                  
one function to create a guid,
and one function to control if a guid is valid.
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form was submitted, process the data
    makebooking();
}



function client()
{
    $client = new Client([
        // Base URI is used with relative requests
        'base_uri' => 'https://www.yrgopelag.se/centralbank/',
        // You can set any number of default request options.
        'timeout'  => 2.0,
    ]);
    return $client;
}

function connect(string $dbName): object
{
    $dbPath = __DIR__ . '/' . $dbName;
    $db = "sqlite:$dbPath";

    // Open the database file and catch the exception if it fails.
    try {
        $db = new PDO($db);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Failed to connect to the database";
        throw $e;
    }
    return $db;
}

function guidv4(string $data = null): string
{
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function isValidUuid(string $uuid): bool
{
    if (!is_string($uuid) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1)) {
        return false;
    }
    return true;
}


function getOneRoom(string $roomId): array
{
    // Connect to the database using the connect function
    $dbName = "hotel.db";
    $db = connect($dbName);


    // Prepare the SQL statement
    $query = $db->prepare('SELECT * FROM rooms WHERE id = :roomId');

    // Bind the parameter
    $query->bindParam(':roomId', $roomId, PDO::PARAM_INT);

    // Execute the query
    $query->execute();

    // Fetch the result as an associative array
    $room = $query->fetch(PDO::FETCH_ASSOC);
    return $room;
}
function insertBooking(string $startDate, string $endDate, string $mealPreference, string $transfercode, string $roomId)
{
    $dbName = "hotel.db";
    $db = connect($dbName);
    try {
        // Prepare the SQL statement
        $query = $db->prepare("INSERT INTO booking (arrival_date, departure_date, transfercode, room_id)
        VALUES (:startDate, :endDate, :transfercode, :room_id)");

        // Bind the parameter
        $query->bindParam(':startDate', $startDate);
        $query->bindParam(':endDate', $endDate);
        $query->bindParam(':transfercode', $transfercode);
        $query->bindParam(':room_id', $roomId);

        // Execute the query
        $query->execute();

        $bookingId = $db->lastInsertId();
        if ($mealPreference != 0) {
            $query = $db->prepare("INSERT INTO booking_features (booking_id, feature_id)
            VALUES (:bookingId, :featureId)");

            // Bind parameters
            $query->bindParam(':bookingId', $bookingId);
            $query->bindParam(':featureId', $mealPreference);

            // Execute the query
            $query->execute();
        }
        return true;
    } catch (PDOException $e) {
        // Handle exceptions (e.g., log the error or show a user-friendly message)
        echo "Error: " . $e->getMessage();
    } finally {
        // Close the database connection
        $db = null;
    }
}


function totalDates(string $startDate, string $endDate): int
{
    $startDate = new DateTime($startDate);
    $endDate = new DateTime($endDate);

    $interval = $startDate->diff($endDate);
    $totalDays = $interval->days;
    return $totalDays + 1;
}

function makeBooking()
{
    $client = client();
    if (
        isset($_POST["datefilter"]) &&
        isset($_POST["transfercode"]) &&
        isset($_POST["pricePerNight"]) &&
        isset($_POST["id"])
    ) {
        // Use htmlspecialchars to sanitize user input

        $dates = trim(htmlspecialchars($_POST["datefilter"]));
        $transferCode = htmlspecialchars($_POST["transfercode"]);
        $mealPreference = ($_POST["meal_preference"] ?? "0") !== "0" ? htmlspecialchars($_POST["meal_preference"]) : "0";
        $pricePerNight = htmlspecialchars($_POST["pricePerNight"]);
        $roomId = htmlspecialchars($_POST["id"]);
        $fullDates = explode("-", $dates);
        $startDate =  $fullDates[0];
        $endDate =  $fullDates[1];

        $totalDates = totalDates($startDate, $endDate);
        $totalCost = $pricePerNight * $totalDates;
        echo $totalCost;
        if (isValidUuid($transferCode)) {

            try {
                $response = $client->request('POST', 'transferCode', [
                    'form_params' => [
                        'transferCode' => $transferCode,
                        'totalcost' => $totalCost
                    ]
                ]);
                $response = json_decode($response->getBody()->getContents());

                if ($response->amount > $totalCost) {
                    //if the booking insertion is successfull, claim the money from the big bank
                    if (insertBooking($startDate, $endDate, $mealPreference, $transferCode, $roomId)) {
                        echo "test";
                        try {
                            $claimed = $client->request('POST', 'deposit', [
                                'form_params' => [
                                    'user' => $_ENV['USER_NAME'],
                                    'transfercode' => $transferCode
                                ]
                            ]);
                            //TODO: THEN WE NEED TO DO THE JSON RESPONSE 
                            bookingResponse();
                        } catch (Exception $e) {

                            echo $e->getMessage();
                        }
                    }
                } else {
                    echo "Your transfercode doesn't cover the total cost.<br/> Total Cost: $totalCost <br/>Transfercode Amount:$response->amount";
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            echo "Error: Transfercode is not valid";
        }
    } else {
        // Handle the case where one or more variables are not set
        // You might want to display an error message or take appropriate action
        echo "Error: Missing one or more form fields.";
    }
}


function bookingResponse()
{
    //do some shit with the data 

    $response = [
        [
            "id" => 1,
            "name" => "Hammare Thor",
            "price" => 299
        ],
        [
            "id" => 2,
            "name" => "Hyvel Florence-10A",
            "price" => 1299,
            "rating" => 2.34
        ],
    ];
    header('Content-Type:application/json');

    echo json_encode($response);
}
