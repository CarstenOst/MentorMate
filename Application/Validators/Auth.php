<?php

namespace Application\Validators;
class Auth
{
    /**
     * Start the session if it is not started
     * @return void
     */
    public static function startSession(): void
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

    public static function authenticate(string $inputPassword, string $storedPassword): bool
    {
        self::startSession();

        if ($inputPassword && password_verify($inputPassword, $storedPassword)) {
            session_regenerate_id(); // To prevent fixation attacks, see source;
            // https://stackoverflow.com/questions/22965067/when-and-why-i-should-use-session-regenerate-id

            $_SESSION['loggedIn'] = true;

            return true;
        } else {
            return false;
        }
    }

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

