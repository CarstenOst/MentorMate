<?php

namespace Application\Views\User;

require("../../../autoloader.php");

use Core\Entities\User;
use Application\Validators\Auth;
use Application\Views\Shared\Layout;
use Application\Validators\Validator;
use Application\Validators\InputHandler;
use Application\Views\Shared\HtmlRenderer;
use Infrastructure\Repositories\ErrorCode;
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
     *
     * @param array $formData the form fields and values as an associated matrix
     * @return int the id of the inserted user
     */
    private static function registerUser(array $formData, &$notValidResponseMessage): int
    {
        // Creates the user, and sends this to the database
        $user = new User();
        $user->setFirstName(self::capitalizeFirstLetter($formData[self::FIRST_NAME][0]));
        $user->setLastName(self::capitalizeFirstLetter($formData[self::LAST_NAME][0]));
        $user->setEmail($formData[self::EMAIL][0]);
        $user->setPassword(password_hash($formData[self::PASSWORD][0], PASSWORD_BCRYPT));
        $user->setUserType(self::capitalizeFirstLetter($formData[self::USER_TYPE][0]));
        $user->setAbout("");

        // If the user was created, we want to authenticate the user, and exit the script
        if (($createdUserId = UserRepository::create($user)) > 0) {
            // This chains the database queries, which is not the best thing to do
            // TODO less coupling, move this out of here, or change function name
            Auth::authenticate($formData[self::PASSWORD][0], $formData[self::EMAIL][0]);
            return $createdUserId;
        }

        // If the email already exists, we want to tell the user. We should not tell the user, or
        // add a cool down to the register button, to prevent brute force attacks.
        // Here we could add a session keeping count of how many times the user has tried to register
        if ($createdUserId == -ErrorCode::DUPLICATE_EMAIL) {
            $notValidResponseMessage[] = "Your email already exists. Try to login or use another email";
            return -ErrorCode::DUPLICATE_EMAIL;
        }
        $notValidResponseMessage[] = "Something went wrong. 
        Please try again later, and contact an admin if the problem persists.";
        return 0;
    }

    private static function notValidResponse(array $notValidResponseMessage, $dataInput): void
    {
        HtmlRenderer::generateResponse($notValidResponseMessage, false,
            count($notValidResponseMessage) * 1500 + 1000); // Time until death! (of msg)
        self::renderPage($dataInput);
    }

    /**
     * Function for formatting a name so each word's first letter is capitalized
     * @param string $name a string name to format so each word's first letter capitalized
     *
     * @return string of the name formatted with each word's first letter capitalized
     */
    public static function capitalizeFirstLetter(string $name): string
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

        //TODO ADD LENGTH RESTRICTION OF 255 CHARS!

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

        // Try to register the user. If the user was registered, we want to authenticate the user.
        self::registerUser($dataInput, $notValidResponseMessage);
        // if register user returns a negative number, it means an error code was triggered,
        // such as duplicate email for example.
        if (!empty($notValidResponseMessage)) {
            self::notValidResponse($notValidResponseMessage, $dataInput);
        }

        // If we get here, then the user is authenticated.
        // Now we could send an email verification to the user, but since we use fake emails, we will not
        // do that.
        // Just change the header to view the profile:
        header("Location: Profile.php");
        exit();

    }
}


// If the request method is POST, we want to process the form, else we want to render the page with session data if any.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    Register::processForm();
} else {
    Register::renderPage();
}
