<?php

declare(strict_types=1);
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use GuzzleHttp\Client;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();



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

function redirect(string $path)
{
    header("Location: $path");
    exit;
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
function insertBooking(string $startDate, string $endDate, int $mealPreference, string $transfercode, string $roomId, int $totalCost)
{
    $dbName = "hotel.db";
    $db = connect($dbName);
    try {
        // Prepare the SQL statement
        $query = $db->prepare("INSERT INTO booking (arrival_date, departure_date, transfercode, total_cost, room_id)
        VALUES (:startDate, :endDate, :transfercode, :total_cost, :room_id)");

        // Bind the parameter
        $query->bindParam(':startDate', $startDate);
        $query->bindParam(':endDate', $endDate);
        $query->bindParam(':transfercode', $transfercode);
        $query->bindParam(':room_id', $roomId);
        $query->bindParam(':total_cost', $totalCost);

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
        return $bookingId;
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
    $_SESSION['errors'] = [];
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
        $mealPreference = ($_POST["meal_preference"] ?? 0) !== 0 ? (int) htmlspecialchars($_POST["meal_preference"]) : 0;
        $pricePerNight = htmlspecialchars($_POST["pricePerNight"]);
        $roomId = htmlspecialchars($_POST["id"]);
        $fullDates = explode("-", $dates);
        $startDate =  str_replace("/", "-", trim($fullDates[0]));
        $endDate =  str_replace("/", "-", trim($fullDates[1]));

        //TODO:: ALSO UDATE THE FORM TO CHECKBOX SELECTION FOR THE OPTIONS Need to make a method that iterates over all of these and return the total value if there is more than one preference/extra
        $mealPreferenceCost = ($mealPreference == 0) ? 0 : (int) getFeature($mealPreference)["price"];

        $totalDates = totalDates($startDate, $endDate);
        $totalCost = ($pricePerNight * $totalDates) + $mealPreferenceCost;


        if (isValidUuid($transferCode)) {

            try {
                $response = $client->request('POST', 'transferCode', [
                    'form_params' => [
                        'transferCode' => $transferCode,
                        'totalcost' => $totalCost
                    ]
                ]);

                $response = json_decode($response->getBody()->getContents());

                if (isset($response->error)) {
                    $_SESSION['errors'][] = 'The key is not validated at the server try a different one!';
                    redirect("room.php?room=" . $_POST["id"]);
                    exit;
                } else {
                    if ($response->amount >= $totalCost) {
                        $bookingId = (int) insertBooking($startDate, $endDate, $mealPreference, $transferCode, $roomId, $totalCost);
                        //if the booking insertion is successfull, claim the money from the big bank
                        if (isset($bookingId)) {
                            try {
                                $claimed = $client->request('POST', 'deposit', [
                                    'form_params' => [
                                        'user' => $_ENV['USER_NAME'],
                                        'transferCode' => $transferCode
                                    ]
                                ]);
                                bookingResponse($bookingId);
                            } catch (Exception $e) {

                                echo $e->getMessage();
                            }
                        }
                    } else {
                        $_SESSION['errors'][] = "Error: Your transfercode doesn't cover the total cost.<br/> Total Cost: $totalCost <br/>Transfercode Amount:$response->amount";
                        redirect("room.php?room=" . $_POST["id"]);
                        exit;
                    }
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            $_SESSION['errors'][] = "Error: Transfercode is not valid";
            redirect("room.php?room=" . $_POST["id"]);
            exit;
        }
    } else {
        // Handle the case where one or more variables are not set
        // You might want to display an error message or take appropriate action
        $_SESSION['errors'][] = "Error: Missing one or more form fields.";
        redirect("room.php?room=" . $_POST["id"]);
        exit;
    }
}


function bookingResponse(int $bookingId)
{
    //do some shit with the data 
    $booking = getBooking($bookingId);
    $features = getFeatuers($bookingId);

    $externalGreeting = ["greeting" => "Thank you for choosing Jurassic Hotel", "imageUrl" => "/images/thank-you.jpg"];
    $response = [
        [
            "island" => $_ENV["ISLAND"],
            "name" => $_ENV["HOTEL"],
            "arrival_date" => $booking["arrival_date"],
            "departure_date" => $booking["departure_date"],
            "total_cost" => $booking["total_cost"],
            "stars" => $_ENV["STARS"],
            "features" => $features,
            "addtional_info" => $externalGreeting,
        ]
    ];

    header('Content-Type:application/json');
    echo json_encode($response);
}

function getBooking(int $bookingId)
{
    // Connect to the database using the connect function
    $dbName = "hotel.db";
    $db = connect($dbName);


    // Prepare the SQL statement
    $query = $db->prepare('SELECT * FROM booking WHERE id = :bookingId');

    // Bind the parameter
    $query->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);

    // Execute the query
    $query->execute();

    // Fetch the result as an associative array
    $booking = $query->fetch(PDO::FETCH_ASSOC);
    return $booking;
}
function getFeatuers(int $bookingId)
{
    $dbName = "hotel.db";
    $db = connect($dbName);


    // Prepare the SQL statement
    $query = $db->prepare("SELECT features.type, features.price
    FROM booking_features 
    INNER JOIN features ON booking_features.feature_id = features.id
    WHERE booking_features.booking_id = :bookingId");

    // Bind the parameter
    $query->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);

    // Execute the query
    $query->execute();

    // Fetch the result as an associative array
    $features = $query->fetchAll(PDO::FETCH_ASSOC);
    return $features;
}

function getFeature(int $featureId)
{
    $dbName = "hotel.db";
    $db = connect($dbName);


    // Prepare the SQL statement
    $query = $db->prepare("SELECT type, price
    FROM features
    WHERE id = :featureId");

    // Bind the parameter
    $query->bindParam(':featureId', $featureId, PDO::PARAM_INT);

    // Execute the query
    $query->execute();

    // Fetch the result as an associative array
    $feature = $query->fetch(PDO::FETCH_ASSOC);
    return $feature;
}
