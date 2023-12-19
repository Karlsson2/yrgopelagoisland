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
</body>

</html>