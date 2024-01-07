<?php
// Include Composer's autoloader to load required dependencies
require 'vendor/autoload.php';

// Include header.php for consistent header across pages
require __DIR__ . "/header.php";

// Include hotelFunctions.php to access functions for retrieving rooms and features
require __DIR__ . "/hotelFunctions.php";

// Retrieve all rooms and features from functions in hotelFunctions.php
$rooms = getAllRooms();
$features = getallFeatures();
?>

<!-- HTML code for the index page starts here -->

<!-- Hero section with background image and introductory text -->
<div class="index-header-image" style="background-image: url('images/index-header.jpg');">
    <!-- Overlay div for styling -->
    <div class="before"></div>

    <!-- Header text container with title, subtitle, and chevron for scrolling -->
    <div class="header-text-container">
        <div class="header-title"><span class="coral-text">More than a hotel...</span></div>
        <div class="header-subtitle"> here life will find a way</div>
    </div>

    <!-- Chevron container for scrolling down -->
    <div class="chevron-container"><a href="#about-us"><i class="fa-solid fa-chevron-down"></i></a></div>
</div>

<!-- Dark background section with information about the hotel -->
<div class="dark-background">
    <div class="container">
        <div class="about-us-container" id="about-us">
            <div class="about-us">About Us <span class="coral-text">.</span></div>
            <div class="about-us-text">
                Welcome to Jurassic Hotel, an extraordinary retreat on a lush Jurassic island.
                Our hotel invites you to step into a world frozen in time, where the enchanting
                allure of ancient dinosaurs coexists with modern luxury.<br /><br /> Nestled amid
                breathtaking scenery, our accommodations seamlessly blend comfort with adventure.
                Whether you're an intrepid explorer seeking <a href="/activities.php" class="coral-text">adventure</a>,
                a nature enthusiast, or a family seeking a unique getaway, Jurassic Hotel promises
                an unforgettable experience.
            </div>
        </div>
    </div>
</div>

<!-- Coral background section with information about multi-day booking discounts -->
<div class="coral-background">
    <div class="container index-discount-container">
        <div class="discount-icon">
            <div class="discount-icon-text">%</div>
        </div>
        <div class="index-discounts">Multi-day booking discounts available for all rooms.</div>
    </div>
</div>

<!-- Light background section with a slider for displaying different room cards -->
<div class="container light-background" id="rooms">
    <!-- Slider main container -->
    <div class="room-container">
        <div class="container-title">Our Rooms</div>

        <!-- Swiper slider for displaying room cards -->
        <div class="swiper">
            <!-- Additional required wrapper -->
            <div class="swiper-wrapper">
                <!-- Slides -->
                <?php foreach ($rooms as $room) : ?>
                    <div class="room-card swiper-slide">
                        <!-- Link to individual room page -->
                        <a href="/room.php?room=<?= $room["id"] ?>">
                            <!-- Image container with room image -->
                            <div class="image-container">
                                <div class="room-image" style="background-image:url(<?= $room["image1"] ?>); background-position: center;background-size: cover;">
                                </div>
                            </div>
                        </a>

                        <!-- Card room information with details and booking link -->
                        <div class="card-room-info">
                            <div class="info-head">
                                <!-- Room title and price per night -->
                                <div class="room-title"><?= $room["category"] ?></div>
                                <div class="price">$<?= $room["price_per_night"] ?>/night</div>
                            </div>
                            <div class="info-more">
                                <div class="features">
                                    <!-- Room features: beds, view, air conditioning -->
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
                                <!-- Booking button with link to individual room page -->
                                <div class="button-div">
                                    <a href="/room.php?room=<?= $room["id"] ?>" class="button">
                                        <span class="button-text">Book now </span><i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Swiper navigation buttons -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
</div>

<!-- Dark background section with information about hotel activities -->
<div class="dark-background">
    <div class="container">
        <div class="activities-container">
            <div class="content-container-activities">
                <div class="activities-text">
                    <!-- Title and description of hotel activities -->
                    <div class="container-title">The Activities <span class="coral-text">.</span></div>
                    <div class="activities-description">
                        Embark on an unforgettable adventure at our dinosaur-themed hotel with four exhilarating activities.
                        Soar through the skies as you experience the thrill of <span class="coral-text">Flying with Dinosaurs</span>,
                        take a dip into prehistoric waters with <span class="coral-text">Swimming with Dinosaurs</span>, witness
                        the magic of life at the <span class="coral-text">Incubation room</span>, and explore the wonders of a bygone
                        era on an exciting <span class="coral-text">Dinosaur Safari</span>. Immerse yourself in the fascinating
                        world of dinosaurs, creating memories that will last a lifetime in these unique and thrilling hotel experiences.
                    </div>
                    <!-- Button to read more about activities -->
                    <div class="button-div">
                        <a href="/activities.php" class="activities-button">
                            <span class="button-text">Read more </span><i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <!-- Image container for displaying activity images -->
                <div class="activities-images">
                    <img src="<?= $features[1]["image"] ?>" class="activities-image-<?= $key ?>" alt="">
                </div>
            </div>
        </div>