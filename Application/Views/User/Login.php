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