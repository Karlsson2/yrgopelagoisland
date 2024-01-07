<?php
// Include Composer's autoloader to load required dependencies
require 'vendor/autoload.php';

// Include header.php for consistent header across pages
require __DIR__ . "/header.php";

// Include hotelFunctions.php to access functions for retrieving room data
require __DIR__ . "/hotelFunctions.php";

// Check if the "room" parameter is set in the URL
if (isset($_GET["room"])) {
    // If the ID has been set, get the room from the database
    $room = getOneRoom($_GET["room"]);
}

// Retrieve discounts and features from functions in hotelFunctions.php
$discounts = getDiscounts((int) $room["id"]);
$features = getAllFeatures();
?>

<!-- HTML code for the individual room page starts here -->

<!-- Header image section with background image -->
<div class="header-image" style="background-image: url('<?= $room["image1"] ?>');">
    <!-- Overlay div for styling -->
    <div class="before"></div>
</div>

<!-- Light background section with room information -->
<div class="light-background">
    <div class="room-info-container">
        <div class="room-info">
            <!-- Room name, cost per night, and basic features -->
            <div class="room-info-head">
                <div class="room-name"><?= $room["category"] ?></div>
                <div class="room-cost">$<?= $room["price_per_night"] ?>/night</div>
            </div>
            <div class="room-features">
                <div class="beds"><i class="fa-solid fa-bed"></i> Sleeps <?= $room["sleeps"] ?></div>
                <div class="view">
                    <?php if ($room["view"] == "Jungle") : ?>
                        <i class="fa-solid fa-tree"></i> Jungle View
                    <?php else : ?>
                        <i class="fa-solid fa-umbrella-beach"></i> Beach View
                    <?php endif; ?>
                </div>
                <div class="aircon">
                    <?php if ($room["aircon"]) : ?>
                        <i class="fa-regular fa-snowflake"></i> Aircon
                    <?php else : ?>
                        <i class="fa-regular fa-snowflake"></i> No Aircon
                    <?php endif; ?>
                </div>
            </div>
            <!-- Room description -->
            <div class="room-description"><?= $room["description"] ?></div>
        </div>
    </div>
</div>

<!-- Container for room page content -->
<div class="container room-page-container">
    <?php if (!empty($discounts)) : ?>
        <!-- Display discounts if available -->
        <div class="discounts discount-container">
            <?php foreach ($discounts as $discount) : ?>
                <div class="discount ">
                    <div class="discount-description" data-percentage="<?= $discount["discount_percentage"] ?>" data-days="<?= $discount["days_required"] ?>">
                        <?= $discount["description"] ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['errors'])) : ?>
        <!-- Display errors if any -->
        <div class='errors' id='errors'>
            <?php foreach ($_SESSION['errors'] as $error) : ?>
                <div class="error"> <?= $error ?> </div>
                <?php $_SESSION['errors'] = []; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Form for booking a stay -->
    <div class="form-div">
        <div class="form-title">Book a Stay</div>
        <form action="hotelFunctions.php" method="POST" class="booking-form">
            <!-- Hidden input to identify the booking form submission -->
            <input type="hidden" name="bookingForm" value="1">

            <!-- Date range selection input -->
            <label for="datefilter">Select a Date</label>
            <div class="input-wrapper">
                <div class="icon-container"><i class="fa-regular fa-calendar"></i></div>
                <input type="text" placeholder="Select a date..." name="datefilter" id="datefilter" required>
            </div>

            <!-- Transfercode input -->
            <label for="transfercode">Transfercode</label>
            <div class="input-wrapper">
                <div class="icon-container"><i class="fa-solid fa-key"></i></div>
                <input type="text" name="transfercode" placeholder="Enter Transfercode here" required>
            </div>

            <!-- Section for selecting additional activities -->
            <div class="activities-title">Include an activity</div>
            <div class="activities-card-container">
                <?php foreach ($features as $feature) : ?>
                    <div class="room-activities-card">
                        <img src="<?= $feature["image"] ?>" alt="">
                        <input data-price="<?= $feature["price"] ?>" class="feature-<?= $feature["id"] ?>" type="checkbox" name="selected_features[]" value="<?= $feature["id"] ?>">
                        <div class="feature-description"><span class="coral-text">$<?= $feature["price"] ?></span> - <?= $feature["name"] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Display subtotal, discount, and total price -->
            <div class="subtotal">Subtotal: $0</div>
            <div class="discountTotal"></div>
            <div class="total">Total Price: $0</div>

            <!-- Hidden inputs for form data -->
            <input type="hidden" name="id" value="<?= $room["id"] ?>">
            <input type="hidden" name="pricePerNight" id="pricePerNight" value="<?= $room["price_per_night"] ?>">

            <!-- Submit button for booking -->
            <button type="submit">Book <i class="fa-solid fa-arrow-right"></i></button>
        </form>
    </div>
</div>

<!-- Include footer for consistency -->
<?php require __DIR__ . "/dark-footer.php"; ?>

<!-- Include required scripts for date range picker and custom room script -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="room-script.js"></script>
</body>

</html>