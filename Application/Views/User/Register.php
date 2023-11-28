<?php

namespace Application\Views\User;

include 'InputHandler.php';
require("../../../autoloader.php");

use Exception;
use Core\Entities\User;
use Application\Validators\Auth;
use Application\Views\Shared\Layout;
use Application\Validators\Validator;
use Application\Views\Shared\HtmlRenderer;
use Infrastructure\Repositories\UserRepository;

class Register
{
    public const FIRST_NAME = 'firstName';
    public const LAST_NAME = 'lastName';
    public const EMAIL = 'email';
    public const PASSWORD = 'password';
    public const USER_TYPE = 'userType';

    public const FORM_FIELDS = [
        self::FIRST_NAME => "First Name",
        self::LAST_NAME => "Second Name",
        self::EMAIL => "Email",
        self::PASSWORD => "Password",
        self::USER_TYPE => "User Type"
    ];

    /**
     * Registers a user using the form field values
     * @param array $formData the form fields and values as an associated matrix
     * @return int the id of the inserted user
     */
    public static function registerUser(array $formData): int
    {
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
     * Function for formatting a name so each word's first letter is capitalized
     * @param string $name a string name to format so each word's first letter capitalized
     *
     * @return string of the name formatted with each word's first letter capitalized
     */
    public static function formatName(string $name): string
    {
        return mb_convert_case(
            mb_strtolower($name), MB_CASE_TITLE, "UTF-8"
        );
    }

    /**
     * Renders the page with the form
     *
     * @param array $userInput - optional, if not given, will render the form with empty inputs
     * @return void - echos the html code
     */
    public static function renderPage(array $userInput = []): void
    {
        Layout::displayTop();
        echo "<h2>Register</h2>";
        HtmlRenderer::renderFormArrayBased(
            array_keys(self::FORM_FIELDS),  // Get the keys from the input fields array
            self::FORM_FIELDS,          // Get the labels for the form
            $userInput                           // Get the user inputs
        );
        echo "<p><small>Already a user?</small></p>
        <a href='./Login.php'>Login</a>";
    }

    /**
     * Processes the form with validation and such
     *
     * @return void - echos the html code (by using renderPage() and the generateResponse()).
     */
    public static function processForm(): void
    {
        $handler = new InputHandler(); // Instantiate the InputHandler class

        // Dynamically configure input processing rules.
        // This so I can reuse the InputHandler, rather than hard coding it here.
        // Also, im going to use this code for the project, so I want to make it as reusable as possible.
        $handler->addConfig(self::FIRST_NAME, [Validator::class, 'validateName']);
        $handler->addConfig(self::LAST_NAME, [Validator::class, 'validateName']);
        $handler->addConfig(self::EMAIL, [Validator::class, 'validateEmail'], [Validator::class, 'removeWhiteSpace']);
        $handler->addConfig(self::PASSWORD, [Validator::class, 'validatePassword']);
        $handler->addConfig(self::USER_TYPE, [Validator::class, 'validateUserType']);

        //TODO ADD LENGTH RESTRICTION OF 255!

        // Process inputs based on configured rules, and store them as dataInput and notValidResponseMessage
        list($dataInput, $notValidResponseMessage) = $handler->processInputs($_POST);

        // If validResponseMessage is not empty, we want to display
        // the content with some kind of response and display the form again.
        // We also stop the execution of the rest of this function, cause it already failed.
        if (!empty($notValidResponseMessage)) {
            HtmlRenderer::generateResponse($notValidResponseMessage, false,
                count($notValidResponseMessage) * 1500 + 1000); // Time until death! (of msg)
            self::renderPage($dataInput);
            return;
        }

        // If we get here, we know that the data is valid, and we can create the success message.
        $validMessage = [ // The task asked for array, and array is given
            "User was successfully registered:",
            "First name: {$dataInput[self::FIRST_NAME][0]}",
            "Last name: {$dataInput[self::LAST_NAME][0]}",
            "Email: {$dataInput[self::EMAIL][0]}",
            "Password: {$dataInput[self::PASSWORD][0]}", // This stupid, TODO REMOVE!
            "User type: {$dataInput[self::USER_TYPE][0]}"
        ];

        self::renderPage($dataInput); // Rerender the page with the data from the form. (so we get the success css triggered).
        HtmlRenderer::generateResponse($validMessage, true); // Generate the success message.

        echo "<br><br>"; // Space between the response and the success message.
        echo implode("<br>", $validMessage); // Echo the success message. To keep it visible for the user.
    }
}


// If the request method is POST, we want to process the form, else we want to render the page with session data if any.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    Register::processForm();
} else {
    Register::renderPage();
}

/*
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
*/
