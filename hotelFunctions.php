<?php

declare(strict_types=1);
session_start();
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
    // Form was submitted, check which form it was,
    if (isset($_POST["signupForm"])) {
        $_SESSION['signupMessage'] = [];
        $email = trim(htmlspecialchars($_POST["email"]));
        $redirect = $_POST["redirect"];
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            insertSignup($email);
        } else {
            $_SESSION['signupMessage']['error'] = "Error: Not a valid email address";
        }
        redirect($redirect . "#sign-up");
    }
    if (isset($_POST["bookingForm"])) {
        makebooking();
    }
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

function dinoClient()
{
    $client = new Client([
        // Base URI is used with relative requests
        'base_uri' => 'https://dinosaur-facts-api.shultzlab.com/dinosaurs/random/',
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

function isValidEmail($email)
{
    // Remove illegal characters from email
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Validate email address
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true; // Valid email
    } else {
        return false; // Invalid email
    }
}


function getOneRoom(int $roomId): array
{
    //return one specific room from the db
    $dbName = "hotel.db";
    $db = connect($dbName);
    $query = $db->prepare('SELECT * FROM rooms WHERE id = :roomId');
    $query->bindParam(':roomId', $roomId, PDO::PARAM_INT);
    $query->execute();
    $room = $query->fetch(PDO::FETCH_ASSOC);
    return $room;
}

function getAllRooms(): array
{
    // return all the rooms
    $dbName = "hotel.db";
    $db = connect($dbName);
    $query = $db->prepare('SELECT * FROM rooms');
    $query->execute();
    $rooms = $query->fetchAll(PDO::FETCH_ASSOC);
    return $rooms;
}
function getAllFeatures(): array
{
    //get all the features.
    $dbName = "hotel.db";
    $db = connect($dbName);
    $query = $db->prepare('SELECT * FROM features');
    $query->execute();
    $features = $query->fetchAll(PDO::FETCH_ASSOC);
    return $features;
}

function insertBooking(string $startDate, string $endDate, array $selectedFeatures, string $transfercode, string $roomId, float $totalCost)
{
    $dbName = "hotel.db";
    $db = connect($dbName);
    try {
        $query = $db->prepare("INSERT INTO booking (arrival_date, departure_date, transfercode, total_cost, room_id)
        VALUES (:startDate, :endDate, :transfercode, :total_cost, :room_id)");
        $query->bindParam(':startDate', $startDate);
        $query->bindParam(':endDate', $endDate);
        $query->bindParam(':transfercode', $transfercode);
        $query->bindParam(':room_id', $roomId);
        $query->bindParam(':total_cost', $totalCost);
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
        $_SESSION['errors'][] = 'Error: Could not insert into DB.' . $e;
    }
}


function totalDates(string $startDate, string $endDate): int
{
    //return the total amount of dates booked.
    $startDate = new DateTime($startDate);
    $endDate = new DateTime($endDate);
    $interval = $startDate->diff($endDate);
    $totalDays = $interval->days;
    return $totalDays + 1;
}

function getDino(): string
//get a random dino from the api
{
    $client = dinoClient();

    $response = $client->request('GET', '/dinosaurs/random/name');
    $response = json_decode($response->getBody()->getContents());
    return $response->Name;
}


function makeBooking()
{
    $_SESSION['errors'] = [];
    $_SESSION['success'] = false;
    $_SESSION['response'] = [];
    $client = client();
    if (
        isset($_POST["datefilter"]) &&
        isset($_POST["transfercode"]) &&
        isset($_POST["pricePerNight"]) &&
        isset($_POST["id"])
    ) {
        // Use htmlspecialchars to sanitize user input and collect all the form data for processing

        $dates = trim(htmlspecialchars($_POST["datefilter"]));
        $transferCode = htmlspecialchars($_POST["transfercode"]);
        //TODO: get the price from the DB instead of from the form, even though its "Hidden".
        $pricePerNight = htmlspecialchars($_POST["pricePerNight"]);
        $roomId = htmlspecialchars($_POST["id"]);
        $fullDates = explode("-", $dates);
        $startDate =  str_replace("/", "-", trim($fullDates[0]));
        $endDate =  str_replace("/", "-", trim($fullDates[1]));
        $selectedFeatures = [];
        $totalDates = totalDates($startDate, $endDate);
        $totalFeatureCost = 0;
        $discount = 0;

        if (isset($_POST['selected_features'])) {
            // Loop through the selected checkboxes
            foreach ($_POST['selected_features'] as $selectedFeatureId) {
                // Process each selected feature ID as needed
                $selectedFeatures[] = (int) $selectedFeatureId;
                $feature = getFeature((int) $selectedFeatureId);
                $totalFeatureCost = $totalFeatureCost + $feature["price"];
            }
        }

        $possibleDiscount = getMaxApplicableDaysDiscount($roomId, $totalDates);
        if (!empty($possibleDiscount)) {
            $discount = $possibleDiscount["discount_percentage"];
        }

        $beforeDiscount = (($pricePerNight * $totalDates) + $totalFeatureCost);
        $totalCost = $beforeDiscount - ($beforeDiscount * $discount);

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
                        $_SESSION['errors'][] = 'Error:The key is not validated at the server, either your code is invalid or you havent withdrawn enough!';
                        redirect("room.php?room=" . $_POST["id"] . "#errors");
                        exit;
                    } else {
                        if ($response->amount >= $totalCost) {

                            $bookingId = (int) insertBooking($startDate, $endDate, $selectedFeatures, $transferCode, $roomId, $totalCost);
                            //if the booking insertion is successfull, claim the money from the big bank
                            if (isset($bookingId)) {
                                $client->request('POST', 'deposit', [
                                    'form_params' => [
                                        'user' => $_ENV['USER_NAME'],
                                        'transferCode' => $transferCode
                                    ]
                                ]);
                                bookingResponse($bookingId);
                                redirect("room.php?room=" . $_POST["id"] . "#booking-success");
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
                    $_SESSION['errors'][] = "Error: Issue trying to validate your transfercode at the server";
                }
            } else {
                $_SESSION['errors'][] = "Error: Transfercode is not correct";
                redirect("room.php?room=" . $_POST["id"] . "#errors");
                exit;
            }
        }
    } else {
        $_SESSION['errors'][] = "Error: Missing one or more form fields.";
        redirect("room.php?room=" . $_POST["id"] . "#errors");
        exit;
    }
}


function bookingResponse(int $bookingId)
{
    //JSON Response when a booking is made.
    $booking = getBooking($bookingId);
    $features = getAllFeaturesWithBooking($bookingId);
    $room = getOneRoom((int) $booking["room_id"]);
    $dino = getDino();
    $externalGreeting = [["greeting" => "Thank you for choosing Jurassic Hotel", "dino" => $dino, "dinoURL" => "https://en.wikipedia.org/wiki/" . $dino]];
    $response = [
        [
            "island" => $_ENV["ISLAND"],
            "name" => $_ENV["HOTEL"],
            "arrival_date" => $booking["arrival_date"],
            "departure_date" => $booking["departure_date"],
            "room_type" => $room["category"],
            "total_cost" => $booking["total_cost"],
            "booking_id" => $booking["id"],
            "stars" => $_ENV["STARS"],
            "features" => $features,
            "additional_info" => $externalGreeting,
        ]
    ];
    json_encode($response);
    $_SESSION["response"] = $response;
}

function getBooking(int $bookingId): array
{
    //return the booking with a particular id
    $dbName = "hotel.db";
    $db = connect($dbName);
    $query = $db->prepare('SELECT * FROM booking WHERE id = :bookingId');
    $query->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);
    $query->execute();
    $booking = $query->fetch(PDO::FETCH_ASSOC);
    return $booking;
}
function getAllFeaturesWithBooking(int $bookingId): array
{
    //function to get all the features for a particular booking
    $dbName = "hotel.db";
    $db = connect($dbName);
    $query = $db->prepare("SELECT features.name, features.price
    FROM booking_features 
    INNER JOIN features ON booking_features.feature_id = features.id
    WHERE booking_features.booking_id = :bookingId");
    $query->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);
    $query->execute();
    $features = $query->fetchAll(PDO::FETCH_ASSOC);
    return $features;
}

function getFeature(int $featureId): array
{
    // Function to get all the feature activities
    $dbName = "hotel.db";
    $db = connect($dbName);
    $query = $db->prepare("SELECT name, price
    FROM features
    WHERE id = :featureId");
    $query->bindParam(':featureId', $featureId, PDO::PARAM_INT);
    $query->execute();
    $feature = $query->fetch(PDO::FETCH_ASSOC);
    return $feature;
}

function getBookings(int $roomId): array
{
    //function to get all the bookings
    $dbName = "hotel.db";
    $db = connect($dbName);
    $query = "SELECT arrival_date, departure_date FROM booking WHERE room_id=:roomId";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':roomId', $roomId, PDO::PARAM_INT);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $bookings;
}

function getDiscounts(int $roomId): array
{
    //Function to get all the discount for a particular room
    $dbName = "hotel.db";
    $db = connect($dbName);
    $query = "SELECT * FROM discounts WHERE room_id=:roomId";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':roomId', $roomId, PDO::PARAM_INT);
    $stmt->execute();
    $discounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $discounts;
}

function loadMadeBookings(int $roomId): string
{
    // Function to get all the bookings.
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
    //function to check if the booking is overlapping any current bookings.
    if ($arrivalDate < '01-01-2024' || $departureDate > '31-01-2024') {
        return true; // Booking is outside the allowed date range of january 2024
    }
    $dbName = "hotel.db";
    $db = connect($dbName);
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

function getMaxApplicableDaysDiscount($roomId, $daysBooked)
{
    $dbName = "hotel.db";
    $db = connect($dbName);
    // Retrieve the discount with the maximum applicable days_required for the specified room
    $query = $db->prepare("SELECT discount_percentage, days_required FROM discounts WHERE room_id = :roomId AND days_required <= :daysBooked ORDER BY days_required DESC LIMIT 1");
    $query->bindParam(':roomId', $roomId, PDO::PARAM_INT);
    $query->bindParam(':daysBooked', $daysBooked, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}
function insertSignup($email)
{
    $dbName = "hotel.db";
    try {
        $db = connect($dbName);
        $query = $db->prepare("INSERT INTO signups (email) VALUES (:email)");
        $query->bindParam(':email', $email);
        $query->execute();
        $_SESSION['signupMessage']['success'][] = "Signup successful!";
    } catch (PDOException $e) {
        $_SESSION['signupMessage']['error'][] = "Error: Failed to insert signup - " . $e->getMessage();
    }
}
