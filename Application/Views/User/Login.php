<?php

namespace Application\Views\User;

require("../../../autoloader.php");

use Application\Validators\Auth;
use Application\Views\Shared\Layout;
use Application\Views\Shared\HtmlRenderer;
use Application\Validators\Validator;
use Exception;

class Login
{
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const LOGIN_FIELD = [
        self::EMAIL => 'Insert email',
        self::PASSWORD => 'Insert your password',
    ];

    /**
     * Html component showing the login form
     * @param array $formData the form fields and values as an associated matrix
     *
     * @return void echos the form
     */
    public static function viewLogin(array $formData = []): void
    {
        Layout::displayTop();

        echo "<h2>Login</h2>";
        HtmlRenderer::renderFormArrayBased(
            array_keys(self::LOGIN_FIELD),
            self::LOGIN_FIELD,
            $formData);

        echo "<p><small>Don't already have a user?</small></p>
        <a href='./Register.php'>Register</a>";
    }

}

$formData = $_POST;
// Checks if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST[Login::EMAIL];
    if (Validator::isValid(Validator::EMAIL, $email) && isset($_POST[Login::PASSWORD])) {
        // Logs inn the user
        $loginSuccess = Auth::authenticate($_POST[Login::PASSWORD], $email);
        if ($loginSuccess) {
            header("Location: ../index.php");
            exit();
        } else {
            HtmlRenderer::generateResponse("Wrong password, or email!", false);

            // Do not worry about this, it is just to make the form fields red or green
            $formData[Login::EMAIL] = [$formData[Login::EMAIL], true];
            $formData[Login::PASSWORD] = [$formData[Login::PASSWORD], false];

            Login::viewLogin($formData);
        }
    } else {
        $formData[Login::EMAIL] = [$formData[Login::EMAIL], false];
        $formData[Login::PASSWORD] = [$formData[Login::PASSWORD], false];
        // Submitted form was invalid
        HtmlRenderer::generateResponse("Your email or password is invalid!", false);
        Login::viewLogin($formData);
    }
} else {
    // Displays the login form
    Login::viewLogin($formData);
}
