<?php
namespace  Application\Views\User;

require("../../../autoloader.php");
use Validator;

?>

<html>

    <body>
        <?php
            if (isset($_POST['submit'])) {
                echo "Success!";

            } else {
                Login::viewLogin();
            }

        ?>
    </body>
</html>
