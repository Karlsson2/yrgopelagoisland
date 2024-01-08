<?php if (!empty($_SESSION['signupMessage'])) : ?>
    <div class="message-container">
        <?php if (isset($_SESSION['signupMessage']['error']) && is_array($_SESSION['signupMessage']['error'])) : ?>
            <div class="errors">
                <?php foreach ($_SESSION['signupMessage']['error'] as $errorMessage) : ?>
                    <div class="error"><?= $errorMessage ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['signupMessage']['success']) && is_array($_SESSION['signupMessage']['success'])) :
        ?>
            <div class="successes">
                <?php foreach ($_SESSION['signupMessage']['success'] as $successMessage) : ?>
                    <div class="success"><?= $successMessage ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php unset($_SESSION['signupMessage']); ?>
    </div>
<?php endif; ?>