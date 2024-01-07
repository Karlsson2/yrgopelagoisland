<?php
// Check if the 'signupMessage' key is present in the $_SESSION superglobal and not empty
if (!empty($_SESSION['signupMessage'])) :
?>
    <div class="message-container">

        <?php
        // Check if 'error' messages are present and are in array format
        if (isset($_SESSION['signupMessage']['error']) && is_array($_SESSION['signupMessage']['error'])) :
        ?>
            <div class="errors">
                <?php
                // Loop through each error message and display it in a separate div
                foreach ($_SESSION['signupMessage']['error'] as $errorMessage) :
                ?>
                    <div class="error"><?= $errorMessage ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php
        // Check if 'success' messages are present and are in array format
        if (isset($_SESSION['signupMessage']['success']) && is_array($_SESSION['signupMessage']['success'])) :
        ?>
            <div class="successes">
                <?php
                // Loop through each success message and display it in a separate div
                foreach ($_SESSION['signupMessage']['success'] as $successMessage) :
                ?>
                    <div class="success"><?= $successMessage ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php
        // Unset the 'signupMessage' key from the $_SESSION superglobal to clear the messages after displaying them
        unset($_SESSION['signupMessage']);
        ?>
    </div>
<?php endif; ?>