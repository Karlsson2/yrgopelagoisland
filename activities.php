<?php
require 'vendor/autoload.php';
require __DIR__ . "/header.php";
require __DIR__ . "/hotelFunctions.php";
$random = rand(0, 3);
$features = getAllFeatures();
$randomFeature = $features[$random];
?>
<div class="header-image" style="background-image: url('<?= $randomFeature["image"] ?>');">
</div>
<div class="container activities-container">
    <div class="activities-title">Activities</div>
    <div class="activities">
        <?php foreach ($features as $feature) : ?>
            <div class="activity-card">

                <img src="<?= $feature["image"] ?>" alt="">

                <div class="activity-description">
                    <div class="activity-card-title"><?= $feature["name"] ?></div>
                    <div class="activity-card-price">$ <?= $feature["price"] ?></div>
                    <div class="activity-card-descrition"><?= $feature["description"] ?></div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<?php require __DIR__ . "/dark-footer.php"; ?>