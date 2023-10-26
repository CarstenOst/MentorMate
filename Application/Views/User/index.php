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
                //$isValid = Login::validateFields($formData);
                $isValid = Register::validateFields($formData);
                
                if ($isValid) {
                    //Registers the user
                    $registrationSuccess = Register::registerUser($formData);
                    if ($registrationSuccess) {
                        header("Location: Profile.php");
                        exit();
                    }
                } else {
                    // Submitted form was invalid
                    echo "Error!";
                    Login::viewLogin($formData);
                }
            } else {
                // Displays the login form
                //Login::viewLogin();
                Register::viewRegister();
            }

        ?>
    </body>
</html>
