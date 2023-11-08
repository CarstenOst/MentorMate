<?php

namespace Application\Validators;
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
        return isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true;
    }

    /**
     * Function to check if a user is authentic.
     * This makes $_SESSION['userId'] to be userId if true.
     *
     * @param string $inputPassword must be hashed same way as in the database.
     * @param int $userId the userId for the user from the database.
     * @return bool True if user is authentic, false if not.
     */
    public static function authenticate(string $inputPassword, int $userId): bool
    {
        self::startSession();

        $storedPassword = UserRepository::getUserPassword($userId);

        if ($inputPassword && password_verify($inputPassword, $storedPassword)) {
            session_regenerate_id(); // To prevent fixation attacks

            $_SESSION['userId'] = $userId;

            return true;
        }
        return false;
    }

    /**
     * Function to log out (destroys session).
     * @return void
     */
    public static function logOut()
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
