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
                    echo "Success!";
                    echo "Username: " . $formData['Username'];
                    echo "Password: " . $formData['Password'];
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
