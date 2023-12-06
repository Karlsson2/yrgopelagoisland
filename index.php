<?php
require __DIR__ . "/header.php"
?>

<body>
    <form action="hotelfunctions.php" method="POST">
        <label for="firstname">First Name</label>
        <input type="text" name="firstname" placeholder="Your First Name" required>

        <label for="lastname">Last Name</label>
        <input type="text" name="lastname" placeholder="Your Last Name" required>

        <label for="email">Email Address</label>
        <input type="email" name="email" placeholder="Your Email Address" required>

        <label for="guests">Number of Guests</label>
        <select name="guests" id="guests">
            <option value="1">1</option>
            <option value="2">2</option>
        </select>

        <label for="datefilter">Select a Date</label>
        <div class="input-wrapper">
            <div class="icon-container"><i class="fa-regular fa-calendar"></i></div>
            <input type="text" placeholder="Select a date..." name="datefilter" id="datefilter" required>
        </div>

        <label for="transfercode">Transfercode:</label>
        <input type="text" name="transfercode" placeholder="xxxxxxxx-xxxx-xxx-xxx-xxxxxxxxxxxxx" required>

        <label for="requests">Special Requests</label>
        <input type="text" name="requests" placeholder="Special Requests">

        <label for="meal_preference">Meal Preference</label>
        <select name="meal_preference" id="meal_preference">
            <option value="all_inclusive">All Inclusive</option>
            <option value="half_board">Half Board</option>
            <option value="breakfast_only">Breakfast Only</option>
        </select>

        <button type="submit">Book</button>
    </form>




    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="script.js"></script>
</body>

</html>