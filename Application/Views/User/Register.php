<?php

namespace Application\Views\User;

use Application\Validators\Validator;
use Application\Views\Shared\HtmlRenderer;
use Application\Views\Shared\Layout;
use Core\Entities\User;
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
        $user = new User();
        $user->setFirstName(self::formatName($formData['firstName']));
        $user->setLastName(self::formatName($formData['lastName']));
        $user->setEmail($formData['email']);
        $user->setPassword($formData['password']);
        $user->setUserType('TestType'); // TODO This needs to change with introduction of additional form field
        $user->setAbout("");
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());

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

    /**
     * Function for formatting a name so each word's first letter is capitalized
     * @param name a string name to format so each word's first letter capitalized
     *
     * @return string of the name formatted with each word's first letter capitalized
     */
    public static function formatName($name): string{
        $formattedName = mb_convert_case(
            mb_strtolower(strip_tags($name)), MB_CASE_TITLE, "UTF-8"
        );

        return $formattedName;
    }
}