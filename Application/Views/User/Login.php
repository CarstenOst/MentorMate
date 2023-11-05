<?php

namespace  Application\Views\User;

require("../../../autoloader.php");

use Application\Views\Shared\Layout;
use Application\Views\Shared\HtmlRenderer;
use Application\Validators\Validator;
use Exception;

class Login
{

    /**
     * Validates the form values for the login form
     * @param array $formData the form fields and values as an associated matrix
     *
     * @return boolean indicating if the fields are valid
     * @throws Exception
     */
    public static function validateFields(array $formData): bool
    {
        $validEmail = Validator::isValid('email', $formData['email']);
        $validPassword = Validator::isValid('password', $formData['password']);
        return $validEmail && $validPassword;
    }

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
        $formFields = [
            "email" => "Email",
            "password" => "Password",
        ];

        Layout::displayTop();
        echo "<h2>Login</h2>";
        HtmlRenderer::renderFormArrayBased(array_keys($formFields), $formFields, $formData);
        echo "<p><small>Don't already have a user?</small></p>
        <a href='./Register.php'>Register</a>";
    }

}

?>

<html>
<?php
$formData = $_POST;
// Checks if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $isValid = Login::validateFields($formData);

    if ($isValid) {
        // Logs inn the user
        $loginSuccess = true;
        if ($loginSuccess) {
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
    Login::viewLogin($formData);
}
?>
</html>
