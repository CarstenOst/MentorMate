<?php

namespace Application\Views\User;

use Application\Validators\Validator;
use Application\Views\Shared\HtmlRenderer;
use Application\Views\Shared\Layout;
use Infrastructure\Repositories\UserRepository;

class Register
{

    /**
     * Validates the form values for the register form
     * @param formData the form fields and values as an associated matrix
     *
     * @return boolean indicating if the fields are valid
     */
    public static function validateFields($formData): bool {
        $validFirstName = Validator::isValid('text', $formData['firstName']);
        $validLastName = Validator::isValid('text', $formData['lastName']);
        $validEmail = Validator::isValid('email', $formData['email']);
        $validPassword = Validator::isValid('password', $formData['password']);

        return $validFirstName && $validLastName && $validEmail && $validPassword;
    }

    /**
     * Registers a user using the form field values
     * @param formData the form fields and values as an associated matrix
     *
     * @return boolean indicating the status of the database query
     */
    public static function registerUser($formData): bool {
        // Creates the user, and sends this to the database
        $user = [
            "firstName" => $formData['firstName'],
            "lastName" => $formData['lastName'],
            "email" => $formData['email'],
            "password" => $formData['password'],
            "userType" => "TestType", // TODO This needs to change with introduction of additional form field
            "about" => "",
        ];

        // Returns the status of the sql updating the user
        return UserRepository::create($user);
    }

    /**
     * Html component showing the register user form
     * @param formData the form fields and values as an associated matrix
     *
     * @return void echos the form
     */
    public static function viewRegister($formData = []) {
        $formFields = [
            "firstName" => "First Name",
            "lastName" => "Second Name",
            "email" => "Email",
            "password" => "Password",
        ];

        Layout::displayTop();
        echo "<h2>Register</h2>";
        HtmlRenderer::renderFormArrayBased(array_keys($formFields), $formFields, $formData);

    }
}