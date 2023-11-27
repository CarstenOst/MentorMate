<?php

namespace Application\Views\User;

require ("../../../autoloader.php");

use Application\Validators\Auth;
use Application\Validators\Validator;
use Application\Views\Shared\HtmlRenderer;
use Application\Views\Shared\Layout;
use Core\Entities\User;
use Exception;
use Infrastructure\Repositories\UserRepository;

class Register
{
    private const FIRST_NAME = 'firstName';
    private const LAST_NAME = 'lastName';
    private const EMAIL = 'email';
    private const PASSWORD = 'password';
    private const USER_TYPE = 'userType';

    private const FORM_FIELDS = [
    self::FIRST_NAME => "First Name",
    self::LAST_NAME => "Second Name",
    self::EMAIL => "Email",
    self::PASSWORD => "Password",
    self::USER_TYPE => "User Type"
    ];

    /**
     * Validates the form values for the register form
     * @param array $formData the form fields and values as an associated matrix
     *
     * @return boolean indicating if the fields are valid
     * @throws Exception
     */
    public static function validateFields(array $formData): bool {
        $validFirstName = Validator::isValid(Validator::TEXT, $formData[self::FIRST_NAME]);
        $validLastName = Validator::isValid(Validator::TEXT, $formData[self::LAST_NAME]);
        $validEmail = Validator::isValid(Validator::EMAIL, $formData[self::EMAIL]);
        $validPassword = Validator::isValid(Validator::PASSWORD, $formData[self::PASSWORD]);

        return $validFirstName && $validLastName && $validEmail && $validPassword;
    }

    /**
     * Registers a user using the form field values
     * @param array $formData the form fields and values as an associated matrix
     *
     * @return int the id of the inserted user
     */
    public static function registerUser(array $formData): int {
        // Creates the user, and sends this to the database
        $user = new User();
        $user->setFirstName(self::formatName($formData[self::FIRST_NAME]));
        $user->setLastName(self::formatName($formData[self::LAST_NAME]));
        $user->setEmail($formData[self::EMAIL]);
        $user->setPassword(password_hash($formData[self::PASSWORD], PASSWORD_BCRYPT));
        $user->setUserType($formData[self::USER_TYPE]);
        $user->setAbout("");
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());

        $createdUserId = UserRepository::create($user);

        // This chains the database queries, which might not be the best
        // TODO less coupling
        Auth::authenticate($formData[self::PASSWORD], $formData[self::EMAIL]);
        // Returns the status of the sql updating the user
        return $createdUserId;
    }

    /**
     * Html component showing the register user form
     * @param array $formData the form fields and values as an associated matrix
     *
     * @return void echos the form
     */
    public static function viewRegister(array $formData = []): void { // TODO fix
         // TODO remove this

        Layout::displayTop();
        echo "<h2>Register</h2>";
        HtmlRenderer::renderFormArrayBased(array_keys(self::FORM_FIELDS), self::FORM_FIELDS, $formData);
        echo "<p><small>Already a user?</small></p>
        <a href='./Login.php'>Login</a>";
    }

    /**
     * Function for formatting a name so each word's first letter is capitalized
     * @param string $name a string name to format so each word's first letter capitalized
     *
     * @return string of the name formatted with each word's first letter capitalized
     */
    public static function formatName(string $name): string{
        $formattedName = mb_convert_case(
            mb_strtolower(strip_tags($name)), MB_CASE_TITLE, "UTF-8"
        );

        return $formattedName;
    }
}


$formData = $_POST;
// Checks if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $isValid = Register::validateFields($formData);

    if ($isValid) {
        //Registers the user
        $registrationSuccess = Register::registerUser($formData);
        // if register user returns a negative number, it means an error code was triggered,
        // such as duplicate email for example.
        if ($registrationSuccess >= 0) {
            header("Location: Profile.php");
            exit();
        }
        echo 'Id = ' . $registrationSuccess;
        echo '<br> Your email already exists. Try to login or use another email';
    } else {
        // Submitted form was invalid
        echo "Error!";
        Register::viewRegister($formData);
    }
} else {
    // Displays the register form
    Register::viewRegister($formData);
}
