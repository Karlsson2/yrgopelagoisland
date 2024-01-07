<div class="light-background">
    <div class="footer">
        <div class="sign-up" id="sign-up">
            <form action="/hotelFunctions.php" method="POST">
                <input type="hidden" name="signupForm" value="1">
                <input type="hidden" id="redirectInput" name="redirect" value="">

                <label for="email">Newsletter Signup</label>
                <input type="email" name="email" placeholder="example@email.com">
                <button type="submit">Submit <i class="fa-solid fa-arrow-right"></i></button>
            </form>
        </div>
        <?php require __DIR__ . "/errors.php"; ?>
        <div class="footer-container">
            <div class="logo">Jurassic Hotel</div>
            <div class="footer-links">
                <div class="footer-title">Links</div>
                <a href="index.php#rooms">Rooms</a>
                <a href="/activities.php">Activities</a>
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