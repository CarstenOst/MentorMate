<?php

namespace  Application\Views\User;

require ("../../../autoloader.php");
use Application\Views\Shared\Layout;
use Application\Views\Shared\HtmlRenderer;


class Login
{

    public static function validateFields() {

    }
 public static function viewLogin() {
     $formFields = [
         "Username" => "Username",
         "Password" => "Password",
     ];

     Layout::displayTop();
     HtmlRenderer::renderFormArrayBased(array_keys($formFields), $formFields, $_POST);

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