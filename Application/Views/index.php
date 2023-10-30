<?php
namespace  Application\Views\User;


use Application\Views\Shared\HtmlRenderer;
use Application\Views\Shared\Layout;

require("../../autoloader.php");


// TODO add welcome page, with login page and link to register below
Layout::displayTop();
?>
<body>
    <div>
        <h2>MentorMate</h2>
        <div>
            <a style='text-decoration:none' href='./User/Login.php'>
                <div class='submit-button'>
                    Login
                </div>
            </a>
            <a style='text-decoration:none' href='./User/Register.php'>
                <div class='submit-button'>
                    Register
                </div>
            </a>
        </div>

    </div>

</body>
