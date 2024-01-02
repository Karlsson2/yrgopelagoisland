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


function getOneRoom(int $roomId): array
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

function getAllRooms(): array
{
    // Connect to the database using the connect function
    $dbName = "hotel.db";
    $db = connect($dbName);


    // Prepare the SQL statement
    $query = $db->prepare('SELECT * FROM rooms');

    // Execute the query
    $query->execute();

    // Fetch the result as an associative array
    $rooms = $query->fetchAll(PDO::FETCH_ASSOC);
    return $rooms;
}
function getAllFeatures(): array
{
    // Connect to the database using the connect function
    $dbName = "hotel.db";
    $db = connect($dbName);


    // Prepare the SQL statement
    $query = $db->prepare('SELECT * FROM features');

    // Execute the query
    $query->execute();

    // Fetch the result as an associative array
    $features = $query->fetchAll(PDO::FETCH_ASSOC);
    return $features;
}

function insertBooking(string $startDate, string $endDate, array $selectedFeatures, string $transfercode, string $roomId, float $totalCost)
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


        //if the selectedfeature array is not empty, iterate over the features and insert them in the booking_features table
        if (!empty($selectedFeatures)) {

            foreach ($selectedFeatures as $feature) {
                $query = $db->prepare("INSERT INTO booking_features (booking_id, feature_id)
                VALUES (:bookingId, :featureId)");

                // Bind parameters
                $query->bindParam(':bookingId', $bookingId);
                $query->bindParam(':featureId', $feature);

                // Execute the query
                $query->execute();
            }
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
        $pricePerNight = htmlspecialchars($_POST["pricePerNight"]);
        $roomId = htmlspecialchars($_POST["id"]);
        $fullDates = explode("-", $dates);
        $startDate =  str_replace("/", "-", trim($fullDates[0]));
        $endDate =  str_replace("/", "-", trim($fullDates[1]));
        $selectedFeatures = [];
        $totalDates = totalDates($startDate, $endDate);
        $totalFeatureCost = 0;

        if (isset($_POST['selected_features'])) {
            // Loop through the selected checkboxes
            foreach ($_POST['selected_features'] as $selectedFeatureId) {
                // Process each selected feature ID as needed
                $selectedFeatures[] = (int) $selectedFeatureId;
                $feature = getFeature((int) $selectedFeatureId);
                $totalFeatureCost = $totalFeatureCost + $feature["price"];
            }
        }
        //TODO: add featurecost to the total cost
        $totalCost = ($pricePerNight * $totalDates) + $totalFeatureCost;



        if (isBookingOverlapping($startDate, $endDate, $roomId)) {
            $_SESSION['errors'][] = 'Error: The booking overlaps another booking or is outside the allowed booking scope!';
            redirect("room.php?room=" . $_POST["id"] . "#errors");
            exit;
        } else {
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
                        $_SESSION['errors'][] = 'Error:The key is not validated at the server try a different one!';
                        redirect("room.php?room=" . $_POST["id"] . "#errors");
                        exit;
                    } else {
                        if ($response->amount >= $totalCost) {

                            $bookingId = (int) insertBooking($startDate, $endDate, $selectedFeatures, $transferCode, $roomId, $totalCost);
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
                            } else {
                                $_SESSION['errors'][] = "Error: The money could not be deposited, try again.";
                                redirect("room.php?room=" . $_POST["id"] . "#errors");
                                exit;
                            }
                        } else {
                            $_SESSION['errors'][] = "Error: Your transfercode doesn't cover the total cost.<br/> Total Cost: $totalCost <br/>Transfercode Amount:$response->amount";
                            redirect("room.php?room=" . $_POST["id"] . "#errors");
                            exit;
                        }
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            } else {
                $_SESSION['errors'][] = "Error: Transfercode is not correct";
                redirect("room.php?room=" . $_POST["id"] . "#errors");
                exit;
            }
        }
    } else {
        // Handle the case where one or more variables are not set
        // You might want to display an error message or take appropriate action
        $_SESSION['errors'][] = "Error: Missing one or more form fields.";
        redirect("room.php?room=" . $_POST["id"] . "#errors");
        exit;
    }
}


function bookingResponse(int $bookingId)
{
    //do some shit with the data 
    $booking = getBooking($bookingId);
    $features = getAllFeaturesWithBooking($bookingId);
    $room = getOneRoom((int) $booking["room_id"]);

    $externalGreeting = ["greeting" => "Thank you for choosing Jurassic Hotel", "imageUrl" => "/images/thank-you.jpg"];
    $response = [
        [
            "island" => $_ENV["ISLAND"],
            "name" => $_ENV["HOTEL"],
            "arrival_date" => $booking["arrival_date"],
            "departure_date" => $booking["departure_date"],
            "room_type" => $room["category"],
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
function getAllFeaturesWithBooking(int $bookingId)
{
    $dbName = "hotel.db";
    $db = connect($dbName);


    // Prepare the SQL statement
    $query = $db->prepare("SELECT features.name, features.price
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
    $query = $db->prepare("SELECT name, price
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

function getBookings(int $roomId)
{
    $dbName = "hotel.db";
    $db = connect($dbName);
    $query = "SELECT arrival_date, departure_date FROM booking WHERE room_id=:roomId";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':roomId', $roomId, PDO::PARAM_INT);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $bookings;
}

function getDiscounts(int $roomId)
{
    $dbName = "hotel.db";
    $db = connect($dbName);
    $query = "SELECT * FROM discounts WHERE room_id=:roomId";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':roomId', $roomId, PDO::PARAM_INT);
    $stmt->execute();
    $discounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $discounts;
}

function loadMadeBookings(int $roomId)
{
    // Generate an array of disabled individual dates
    $bookings = getBookings($roomId);
    $disabledDates = [];
    foreach ($bookings as $booking) {
        $startDate = new DateTime($booking['arrival_date']);
        $endDate = new DateTime($booking['departure_date']);

        // Iterate over each date within the range
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $disabledDates[] = $currentDate->format('Y-m-d');
            $currentDate->modify('+1 day');
        }
    }
    return json_encode($disabledDates);
}


function isBookingOverlapping(string $arrivalDate, string $departureDate, string $roomId): bool
{
    if ($arrivalDate < '01-01-2024' || $departureDate > '31-01-2024') {
        return true; // Booking is outside the allowed date range of january 2024
    }
    $dbName = "hotel.db";
    $db = connect($dbName);

    // Prepare the SQL statement
    $query = $db->prepare("SELECT COUNT(*) AS count_overlap
              FROM booking
              WHERE room_id = :room_id
                AND ((arrival_date <= :arrival_date AND departure_date >= :arrival_date)
                     OR (arrival_date <= :departure_date AND departure_date >= :departure_date)
                     OR (arrival_date >= :arrival_date AND departure_date <= :departure_date))");
    $query->bindParam(':room_id', $roomId, PDO::PARAM_INT);
    $query->bindParam(':arrival_date', $arrivalDate);
    $query->bindParam(':departure_date', $departureDate);
    $query->execute();

    $result = $query->fetch(PDO::FETCH_ASSOC);

    return $result['count_overlap'] > 0;
}
