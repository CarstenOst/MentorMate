<?php
namespace  Application\Views\User;

require("../../../autoloader.php");

?>

<html>

    <body>
        <?php
            // Checks if form was submitted
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $formData = $_POST;
                $isValid = Login::validateFields($formData);
                
                if ($isValid) {
                    header("Location: Profile.php"); // Change 'success.php' to the desired URL
                    exit();
                } else {
                    // Submitted form was invalid
                    echo "Error!";
                    Login::viewLogin($formData);
                }
            } else {
                // Displays the login form
                Login::viewLogin();
            }

        ?>
    </body>
</html>
