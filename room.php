<?php

require 'vendor/autoload.php';
require __DIR__ . "/header.php";
require __DIR__ . "/hotelFunctions.php";

$features = getAllFeatures();

if (isset($_GET["room"])) {
    //if the ID has been set, get the room from the database. then generate the room content on the page.
    $room = getOneRoom($_GET["room"]);
}

?>

<div class="header-image" style="background-image: url('<?= $room["image1"] ?>');">
</div>

<div class="light-background">
    <div class="room-info-container">
        <div class="room-info">
            <div class="small-images">
                <div class="small-image">
                    <img src="<?= $room["image2"] ?>" class="image-square" alt="">
                </div>
                <div class="small-image">
                    <img src="<?= $room["image3"] ?>" class="image-square" alt="">
                </div>
            </div>
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
            <div class="room-description"><?= $room["description"] ?></div>
        </div>
    </div>
</div>
<div class="container room-page-container">
    <?php
    // Check if there are any errors
    if (!empty($_SESSION['errors'])) {
        echo "<div class='errors' id='errors'>";
        foreach ($_SESSION['errors'] as $error) {
            echo '<div class="error">' . htmlspecialchars($error) . '</div>';
        }
        $_SESSION['errors'] = [];
        echo "</div>";
    }
    ?>
    <div class="form-div">
        <div class="form-title">Book a Stay</div>
        <form action="hotelFunctions.php" method="POST" class="booking-form">
            <label for="datefilter">Select a Date</label>
            <div class="input-wrapper">
                <div class="icon-container"><i class="fa-regular fa-calendar"></i></div>
                <input type="text" placeholder="Select a date..." name="datefilter" id="datefilter" required>
            </div>
            <label for="transfercode">Transfercode</label>
            <div class="input-wrapper">
                <div class="icon-container"><i class="fa-solid fa-key"></i></div>
                <input type="text" name="transfercode" placeholder="Enter Transfercode here" required>
            </div>
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
            <div class="total">Total Price: $0</div>
            <input type="hidden" name="id" value="<?= $room["id"] ?>">
            <input type="hidden" name="pricePerNight" id="pricePerNight" value="<?= $room["price_per_night"] ?>">
            <button type="submit">Book <i class="fa-solid fa-arrow-right"></i></button>
        </form>
    </div>
</div>

<?php require __DIR__ . "/dark-footer.php"; ?>
<script type="text/javascript" src="script.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    <?php
    // Your PHP code to generate the disabledDates array
    $disabledDates = loadMadeBookings((int)$room["id"]);
    echo "var disabledDates = " . $disabledDates . ";";
    ?>
    $(function() {
        $('#datefilter').daterangepicker({
                minDate: '01/01/2024',
                maxDate: '31/01/2024',
                autoUpdateInput: false,
                autoApply: true,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'DD/MM/YYYY',
                },
                isInvalidDate: function(date) {
                    // Check if the date is in the array of disabled dates
                    var dateString = date.format('YYYY-MM-DD');
                    return disabledDates.includes(dateString);
                },
            },
            function(start, end, label) {
                // Manually trigger the change event
                console.log(
                    'New date range selected: ' +
                    start.format('DD-MM-YYYY') +
                    ' to ' +
                    end.format('DD-MM-YYYY') +
                    ' (predefined range: ' +
                    label +
                    ')'
                );
            }
        );

        $('input[name="datefilter"]').on(
            'apply.daterangepicker',
            function(ev, picker) {
                $(this).val(
                    picker.startDate.format('DD/MM/YYYY') +
                    ' - ' +
                    picker.endDate.format('DD/MM/YYYY')
                );
            }
        );
        $('input[name="datefilter"]').on(
            'apply.daterangepicker',
            function(ev, picker) {
                $(this).trigger('change');
            }
        );

        $('input[name="datefilter"]').on(
            'cancel.daterangepicker',
            function(ev, picker) {
                $(this).val('');
            }
        );
    });
</script>
<script type="text/javascript" src="room-script.js"></script>
</body>

</html>