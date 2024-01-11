<?php
require __DIR__ . "/hotelFunctions.php";
require 'vendor/autoload.php';
require __DIR__ . "/header.php";



if (isset($_GET["room"])) {
    //if the ID has been set, get the room from the database. then generate the room content on the page.
    $room = getOneRoom($_GET["room"]);
}
$discounts = getDiscounts((int) $room["id"]);
$features = getAllFeatures();
?>

<div class="header-image" style="background-image: url('<?= $room["image1"] ?>');">
    <div class="before"></div>
</div>

<div class="light-background">
    <div class="room-info-container">
        <div class="room-info">
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
    <?php if (!empty($discounts)) : ?>
        <div class="discounts discount-container">
            <?php foreach ($discounts as $discount) : ?>
                <div class="discount ">
                    <div class="discount-description" data-percentage="<?= $discount["discount_percentage"] ?>" data-days="<?= $discount["days_required"] ?>"><?= $discount["description"] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['errors'])) : ?>
        <div class='errors' id='errors'>
            <?php foreach ($_SESSION['errors'] as $error) : ?>
                <div class="error"> <?= $error ?> </div>
                <?php $_SESSION['errors'] = []; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>


    <?php if (isset($_SESSION["response"]) && (is_array($_SESSION["response"]) && count($_SESSION["response"]) !== 0)) : ?>
        <?php $jsonString = json_encode($_SESSION["response"], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
        <div class="booking-success" id="booking-success">
            <div class="booking-success-text">
                <div class="booking-success-title">Your Booking is confirmed!</div>
                <div class="booking-success-subtitle">Booking ref: <span class="coral-text"><?= $_SESSION["response"][0]["booking_id"] ?> </span></div>
                <div class="dino-info"><img src="/images/dino-icon.png" alt="" class="dino-icon">
                    <div class="dino-text">Before your trip, read some more about this cool random dino: <a href="<?= $_SESSION["response"][0]["additional_info"][0]["dinoURL"] ?>"><?= $_SESSION["response"][0]["additional_info"][0]["dino"] ?></a>.</div>
                </div>
                <div class="booking-success-information">Please see your specific JSON below, use the smart clipboard button to copy it!</div>
            </div>

            <pre><code id="codeElement"> <?= $jsonString ?> </code><button id="copyButton"><i class="fa-regular fa-copy"></i></button><div class="code-before"></div><div class="copy-message">Copied to Clipboard!</div></pre>
            <a class="booking-success-button-a" href="room.php?room=<?= $room["id"] ?>">
                <div class="booking-success-button">Book another stay <i class="fa-solid fa-arrow-right"></i></div>
            </a>
        </div>

        <?php unset($_SESSION["response"]); ?>
    <?php else : ?>


        <div class="form-div" id="form-div">
            <div class="form-title">Book a Stay</div>
            <form action="hotelFunctions.php" method="POST" class="booking-form">
                <input type="hidden" name="bookingForm" value="1">
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
                <div class="subtotal">Subtotal: $0</div>
                <div class="discountTotal"></div>
                <div class="total">Total Price: $0</div>
                <input type="hidden" name="id" value="<?= $room["id"] ?>">
                <input type="hidden" name="pricePerNight" id="pricePerNight" value="<?= $room["price_per_night"] ?>">
                <button type="submit">Book <i class="fa-solid fa-arrow-right"></i></button>
            </form>
        </div>

    <?php endif; ?>
</div>

<?php require __DIR__ . "/dark-footer.php"; ?>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/go.min.js"></script>


<script>
    <?php
    // Your PHP code to generate the disabledDates array
    $disabledDates = loadMadeBookings((int)$room["id"]);
    $roomImages = [];
    $roomImages[] = $room["image1"];
    $roomImages[] = $room["image2"];
    echo "const disabledDates1 = " . json_encode($disabledDates) . ";";
    echo "const roomImages1 =" . json_encode($roomImages) . ";";
    ?>
    const disabledDates = JSON.parse(JSON.stringify(disabledDates1));
    const roomImages = JSON.parse(JSON.stringify(roomImages1));
    const backgroundContainer = document.querySelector('.header-image');
    let index = 0;

    setInterval(function() {
        // Update background image
        backgroundContainer.style.backgroundImage = `url(${roomImages[index]})`;

        // Increment the index or reset to 0 if it reaches the end
        index = (index + 1) % roomImages.length;
    }, 3000); // Change background every 5 seconds (adjust as needed)


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


    hljs.highlightAll();
</script>
<script type="text/javascript" src="room-script.js"></script>
</body>

</html>