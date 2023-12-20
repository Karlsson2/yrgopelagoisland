<?php

require 'vendor/autoload.php';
require __DIR__ . "/header.php";
require __DIR__ . "/hotelFunctions.php";



if (isset($_GET["room"])) {
    //if the ID has been set, get the room from the database. then generate the room content on the page.
    $room = getOneRoom($_GET["room"]);
}



?>

<div class="header-image" style="background-image: url('<?= $room["image1"] ?>');">
    <div class="small-images">
        <img src="<?= $room["image2"] ?>" class="image-square" alt="">
        <img src="<?= $room["image3"] ?>" class="image-square" alt="">
    </div>
</div>
<div class="container">
    <div class="room-info">
        <div class="room-name"><?= $room["category"] ?></div>
        <div class="room-cost">$<?= $room["price_per_night"] ?>/night</div>

    </div>
    <div class="errors">
        <?php
        // Check if there are any errors
        if (!empty($_SESSION['errors'])) {
            echo '<ul>';
            foreach ($_SESSION['errors'] as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul>';
            $_SESSION['errors'] = [];
        }
        ?>
    </div>
    <div class="form-div">
        <form action="hotelFunctions.php" method="POST">
            <label for="datefilter">Select a Date</label>
            <div class="input-wrapper">
                <div class="icon-container"><i class="fa-regular fa-calendar"></i></div>
                <input type="text" placeholder="Select a date..." name="datefilter" id="datefilter" required>
            </div>
            <input type="hidden" name="id" value="<?= $room["id"] ?>">
            <input type="hidden" name="pricePerNight" value="<?= $room["price_per_night"] ?>">
            <label for="transfercode">Transfercode</label>
            <input type="text" name="transfercode" placeholder="enter your transfercode here" required>
            <label for="meal_preference">Meal Preference</label>
            <select name="meal_preference" id="meal_preference">
                <option value="" disabled selected hidden>None +$0</option>
                <option value="1">Breakfast +1$</option>
                <option value="2">Half Board +2$</option>
                <option value="3">All Inclusive +3$</option>


            </select>
            <div class="total">$ Total</div>
            <button type="submit">Book</button>
        </form>

    </div>
</div>



<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="script.js"></script>
<script>
    <?php
    // Your PHP code to generate the disabledDates array
    $disabledDates = loadMadeBookings((int)$room["id"]);
    echo "var disabledDates = " . $disabledDates . ";";
    ?>
    console.log(disabledDates);
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
            'cancel.daterangepicker',
            function(ev, picker) {
                $(this).val('');
            }
        );
    });
</script>

</body>

</html>