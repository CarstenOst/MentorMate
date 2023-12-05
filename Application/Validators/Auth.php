<?php

namespace Application\Validators;
use Application\Constants\SessionConst;
use Exception;
use Infrastructure\Repositories\UserRepository;

class Auth
{
    /**
     * Start the session if it is not started
     * @return void
     */
    private static function startSession(): void
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Check if the user is logged in
     * @return bool True if auth checks out, false if not
     */
    public static function checkAuth(): bool
    {
        self::startSession();
        // If session 'loggedIn' is set and is true, return true. Else false
        return isset($_SESSION[SessionConst::LOGGED_IN]) && $_SESSION[SessionConst::LOGGED_IN] === true;
    }

    /**
     * Function to check if a user is authentic.
     * This makes $_SESSION['userId'] to be userId if true.
     *
     * @param string $inputPassword must be hashed same way as in the database.
     * @param string $email
     * @return bool True if user is authentic, false if not.
     * @throws Exception
     */
    public static function authenticate(string $inputPassword, string $email): bool
    {
        self::startSession();

        $user = UserRepository::getUserByEmail($email);
        if (is_string($user)) {
            return false;
        }
        $storedPassword = $user->getPassword();

        if ($inputPassword && password_verify($inputPassword, $storedPassword)) {
            session_regenerate_id(); // To prevent fixation attacks


            $_SESSION[SessionConst::EMAIL] = $user->getEmail();
            $_SESSION[SessionConst::ABOUT] = $user->getAbout();
            $_SESSION[SessionConst::USER_ID] = $user->getUserId();
            $_SESSION[SessionConst::USER_TYPE] = $user->getUserType();
            $_SESSION[SessionConst::FIRST_NAME] = $user->getFirstName();
            $_SESSION[SessionConst::LAST_NAME] = $user->getLastName();
            $_SESSION[SessionConst::CREATED_AT] = $user->getCreatedAt();
            $_SESSION[SessionConst::UPDATED_AT] = $user->getUpdatedAt();

            $_SESSION[SessionConst::LOGGED_IN] = true;

            return true;
        }
        return false;
    }

    /**
     * Function to log out (destroys session).
     * @return void
     */
    public static function logOut(): void
    {
        self::startSession();

        // Unset all session variables
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}
