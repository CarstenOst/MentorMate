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
     * Validates the login credentials against the database values for authentication
     * @param array $formData the form fields and values as an associated matrix
     *
     * @return boolean indicating the status of the query
     * @throws Exception
     */
    public static function loginUser(array $formData): bool
    {
        // TODO replace this with data validation against database password
        $validEmail = Validator::isValid('email', $formData['email']);
        $validPassword = Validator::isValid('password', $formData['password']);
        return $validEmail && $validPassword;
    }

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
    if (Validator::isValid('email', $email)) {
        // Logs inn the user
        $loginSuccess = Auth::authenticate($_POST[Login::PASSWORD], $email);
        if ($loginSuccess) {
            header("Location: Profile.php");
            exit();
        } else {
            echo "Wrong password, or email!";
            Auth::logOut(); // Logout the user TODO remove this
            Login::viewLogin($formData);
        }
    } else {
        // Submitted form was invalid
        echo "Your email is invalid!";
        Login::viewLogin($formData);
    }
} else {
    // Displays the login form
    Login::viewLogin($formData);
}
