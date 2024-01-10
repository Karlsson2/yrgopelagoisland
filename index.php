<?php
require __DIR__ . "/hotelFunctions.php";
require 'vendor/autoload.php';
require __DIR__ . "/header.php";

$rooms = getAllRooms();
$features = getallFeatures();

?>
<div class="index-header-image" style="background-image: url('images/index-header.jpg');">
    <div class="before"></div>
    <div class="header-text-container">
        <div class="header-title"><span class="coral-text">More than a hotel...</span></div>
        <div class="header-subtitle"> here life will find a way</div>
    </div>
    <div class="chevron-container"><a href="#about-us"><i class="fa-solid fa-chevron-down"></i></a></div>
</div>
<div class="dark-background">
    <div class="container">
        <div class="about-us-container" id="about-us">
            <div class="about-us">About Us <span class="coral-text">.</span></div>
            <div class="about-us-text">Welcome to Jurassic Hotel, an extraordinary retreat on a lush Jurassic island. Our hotel invites you to step into a world frozen in time, where the enchanting allure of ancient dinosaurs coexists with modern luxury.<br /><br /> Nestled amid breathtaking scenery, our accommodations seamlessly blend comfort with adventure. Whether you're an intrepid explorer seeking <a href="/activities.php" class="coral-text">adventure</a>, a nature enthusiast, or a family seeking a unique getaway, Jurassic Hotel promises an unforgettable experience.</div>

        </div>
    </div>
</div>
<div class="coral-background">
    <div class="container index-discount-container">
        <div class="discount-icon">
            <div class="discount-icon-text">%</div>
        </div>
        <div class="index-discounts">Multi-day booking discounts available for all rooms.</div>
    </div>
</div>
<div class="container light-background" id="rooms">
    <!-- Slider main container -->
    <div class=" room-container">
        <div class="container-title">Our Rooms</div>

        <div class="swiper">
            <!-- Additional required wrapper -->
            <div class="swiper-wrapper">
                <!-- Slides -->
                <?php foreach ($rooms as $room) : ?>
                    <div class="room-card swiper-slide">
                        <a href="/../room.php?room=<?= $room["id"] ?>">
                            <div class="image-container">
                                <div class="room-image" style="background-image:url(<?= $room["image1"] ?>); background-position: center;background-size: cover;">
                                </div>
                            </div>
                        </a>
                        <div class="card-room-info">
                            <div class="info-head">
                                <div class="room-title"><?= $room["category"] ?></div>
                                <div class="price">$<?= $room["price_per_night"] ?>/night</div>
                            </div>
                            <div class="info-more">
                                <div class="features">
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
                                <div class="button-div">
                                    <a href="/../room.php?room=<?= $room["id"] ?>" class="button">
                                        <span class="button-text">Book now </span><i class="fa-solid fa-arrow-right"></i>

                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
</div>

<div class="dark-background">
    <div class="container">
        <div class="activities-container">
            <div class="content-container-activities">
                <div class="activities-text">
                    <div class="container-title">The Activities <span class="coral-text">.</span></div>
                    <div class="activities-description">Embark on an unforgettable adventure at our dinosaur-themed hotel with four exhilarating activities. Soar through the skies as you experience the thrill of <span class="coral-text">Flying with Dinosaurs</span>,take a dip into prehistoric waters with <span class="coral-text">Swimming with Dinosaurs</span> witness the magic of life at the <span class="coral-text">Incubation room</span> and explore the wonders of a bygone era on an exciting <span class="coral-text">Dinosaur Safari</span>. Immerse yourself in the fascinating world of dinosaurs, creating memories that will last a lifetime in these unique and thrilling hotel experiences.</div>
                    <div class="button-div">
                        <a href="/activities.php" class="activities-button">
                            <span class="button-text">Read more </span><i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="activities-images">

                    <img src="<?= $features[1]["image"] ?>" class="activities-image-<?= $key ?>" alt="">
                </div>

            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . "/light-footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script type="text/javascript" src="index-script.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>


</body>

</html>