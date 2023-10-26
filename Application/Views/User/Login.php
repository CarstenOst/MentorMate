<?php

namespace  Application\Views\User;

require ("../../../autoloader.php");
use Application\Views\Shared\Layout;
use Application\Views\Shared\HtmlRenderer;
use Application\Validators\Validator;

class Login
{

    /**
     * Validates the form values for the login form
     * @param formData the form fields and values as an associated matrix
     *
     * @return boolean indicating if the fields are valid
     */
    public static function validateFields($formData): bool {
        $validEmail = Validator::isValid('email', $formData['email']);
        $validPassword = Validator::isValid('password', $formData['password']);
        return $validEmail && $validPassword;
    }

    /**
     * Validates the login credentials against the database values for authentication
     * @param formData the form fields and values as an associated matrix
     *
     * @return boolean indicating the status of the query
     */
    public static function loginUser($formData): bool {
        // TODO replace this with data validation against database password
        $validEmail = Validator::isValid('email', $formData['email']);
        $validPassword = Validator::isValid('password', $formData['password']);
        return $validEmail && $validPassword;
    }

    /**
     * Html component showing the login form
     * @param formData the form fields and values as an associated matrix
     *
     * @return void echos the form
     */
 public static function viewLogin($formData = []) {
     $formFields = [
         "email" => "Email",
         "password" => "Password",
     ];

     Layout::displayTop();
     echo "<h2>Login</h2>";
     HtmlRenderer::renderFormArrayBased(array_keys($formFields), $formFields, $formData);

 }

}


/*
class Login
{
    public static function validateFields($postData): array
    {
        $errors = [];

        // Validate the Username field (e.g., for a required field)
        if (empty($postData['Username'])) {
            $errors['Username'] = true;
        }

        // Validate the Password field (e.g., for a minimum length)
        if (strlen($postData['Password']) < 8) {
            $errors['Password'] = true;
        }

        return $errors;
    }

    public static function viewLogin($errors = [])
    {
        Layout::displayTop();
        echo '
            <form class="form-group" id="form" action="index.php" method="POST">
                <h2>Login</h2>
                <label for="Username">Username</label>
                <input type="text" class="form-control ';

        // Add 'border border-danger' class if the Username field is not valid
        if (isset($errors['Username'])) {
            echo ' border border-danger';
        }

        echo '" name="Username" id="Username" placeholder="Username">
                <label for="Password">Password</label>
                <input type="password" class="form-control';

        // Add 'border border-danger' class if the Password field is not valid
        if (isset($errors['Password'])) {
            echo ' border border-danger';
        }

        echo '" name="Password" id="Password" placeholder="Password">
                <input class="submit-button" id="pointer" type="submit" value="Login">
            </form>
        ';
    }
}


/*
/*
class Login
{

    public static function validateFields() {

    }
 public static function viewLogin() {
     Layout::displayTop();
     echo '
        <form class="form-group" id="form" action="" method="POST">
            <h2>Login</h2>
            <label for="Username">Username</label>
            <input type="text" style="border-width: 3px !important; " class="form-control border " name="Username" id="Username" placeholder="Username"> <!-- TODO $borderClass in class-->
            <label for="Password">Password</label>
            <input type="password" style="border-width: 3px !important; " class="form-control border " name="Password" id="Password" placeholder="Password">

            <input class="submit-button" id="pointer" type="submit" value="Login">
        </form>
     ';
 }


}

class Login
{
    public static function validateFields($postData): array
    {
        $errors = [];

        // Validate the Username field (e.g., for a required field)
        if (empty($postData['Username'])) {
            $errors['Username'] = true;
        }

        // Validate the Password field (e.g., for a minimum length)
        if (strlen($postData['Password']) < 8) {
            $errors['Password'] = true;
        }

        return $errors;
    }

    public static function viewLogin($errors = [])
    {
        Layout::displayTop();
        echo '
            <form class="form-group" id="form" action="index.php" method="POST">
                <h2>Login</h2>
                <label for="Username">Username</label>
                <input type="text" class="form-control ';

        // Add 'border border-danger' class if the Username field is not valid
        if (isset($errors['Username'])) {
            echo ' border border-danger';
        }

        echo '" name="Username" id="Username" placeholder="Username">
                <label for="Password">Password</label>
                <input type="password" class="form-control';

        // Add 'border border-danger' class if the Password field is not valid
        if (isset($errors['Password'])) {
            echo ' border border-danger';
        }

        echo '" name="Password" id="Password" placeholder="Password">
                <input class="submit-button" id="pointer" type="submit" value="Login">
            </form>
        ';
    }
}
*/