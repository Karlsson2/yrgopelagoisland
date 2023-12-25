<?php
require 'vendor/autoload.php';
require __DIR__ . "/header.php";
require __DIR__ . "/hotelFunctions.php";
$rooms = getAllRooms();
$features = getallFeatures();

?>
<div class="index-header-image" style="background-image: url('images/index-header.jpg');">
    <div class="before"></div>
    <div class="header-text-container">
        <div class="header-title">Jurassic Hotel</div>
        <div class="header-subtitle"><span class="coral-text">More than a hotel...</span> here life will find a way</div>
    </div>
    <div class="chevron-container"><a href="#about-us"><i class="fa-solid fa-chevron-down"></i></a></div>
</div>
<div class="dark-background">
    <div class="container">
        <div class="about-us-container" id="about-us">
            <div class="about-us">About Us <span class="coral-text">.</span></div>
            <div class="about-us-text">Welcome to Jurassic Hotel, an extraordinary retreat on a lush Jurassic island. Our hotel invites you to step into a world frozen in time, where the enchanting allure of ancient dinosaurs coexists with modern luxury.<br /><br /> Nestled amid breathtaking scenery, our accommodations seamlessly blend comfort with adventure. Whether you're an intrepid explorer seeking <a href="#" class="coral-text">adventure</a>, a nature enthusiast, or a family seeking a unique getaway, Jurassic Hotel promises an unforgettable experience.</div>

        </div>
    </div>
</div>
<div class="container light-background" id="rooms">
    <div class="room-container">
        <div class="container-title">Our Rooms</div>
        <div class="card-container">
            <?php foreach ($rooms as $room) : ?>
                <div class="room-card">
                    <div class="image-container">
                        <div class="room-image" style="background-image:url(<?= $room["image1"] ?>); background-position: center;
    background-size: cover;
"> </div>

                    </div>
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
                                <a href="/room.php?room=<?= $room["id"] ?>" class="button">
                                    <span class="button-text">Book now </span><i class="fa-solid fa-arrow-right"></i>

                                </a>
                            </div>

                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
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
                </div>
                <div class="activities-images">

                    <img src="<?= $features[1]["image"] ?>" class="activities-image-<?= $key ?>" alt="">
                    <div class="button-div">
                        <a href="/room.php?room=<?= $room["id"] ?>" class="activities-button">
                            <span class="button-text">Read more </span><i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="light-background">
    <div class="footer">
        <div class="sign-up">
            <form action="#">
                <input type="text" name="email" placeholder="example@email.com">
                <button type="submit">Submit <i class="fa-solid fa-arrow-right"></i></button>
            </form>
        </div>


        <div class="footer-container">
            <div class="logo">Jurassic Hotel</div>
            <div class="footer-links">
                <div class="footer-title">Links</div>
                <a href="#">Rooms</a>
                <a href="#">Activities</a>
                <a href="#">Login</a>
            </div>
            <div class="contact-us">
                <div class="footer-title">Contact us</div>
                <div class="address"><span class="coral-text"><i class="fa-solid fa-location-dot"></i></span> 1 Jurassic Island, Yrgopelag</div>
                <div class="phone"><i class="fa-solid fa-phone"></i> +999 999 999 1</div>
                <div class="email"><i class="fa-solid fa-envelope"></i> <a href="mailto:info@jurassichotel.com">info@jurassichotel.com</a></div>
            </div>

            <div class="social">
                <div class="footer-title">Social</div>
                <a href="#"><i class="fa-brands fa-square-instagram"></i> #JurassicHotel</a>
                <a href="#"><i class="fa-brands fa-square-facebook"></i> /JurassicHotel</a>
                <a href="#"><i class="fa-brands fa-tiktok"></i> @JurassicHotel</a>
                <a href="#"><i class="fa-brands fa-linkedin"></i> /JurassicHotel</a>
                <a href="#"><i class="fa-brands fa-twitter"></i> @JurassicHotel</a>
            </div>
        </div>
    </div>
    <div class="copyright">
        <div class="copyright-text"> Copyright 2023 &copy; Jurassic Hotel</div>
    </div>
</div>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="script.js"></script>
</body>

</html>