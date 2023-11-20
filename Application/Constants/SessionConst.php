<?php

namespace Application\Constants;

class SessionConst
{
    const EMAIL = 'email';
    const ABOUT = 'about';
    const USER_ID = 'userId';
    const LOGGED_IN = 'loggedIn';
    const USER_TYPE = 'userType';
    const LAST_NAME = 'lastName';
    const FIRST_NAME = 'firstName';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    /**
     * Start the session if it is not started, and print it
     * Only debugger function
     * @return void
     */
    public static function sessionDebugger(): void
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        foreach ($_SESSION as $key => $value) {
            if ($key == SessionConst::CREATED_AT || $key == SessionConst::UPDATED_AT) {
                $value = $value->format('Y-m-d H:i:s');
            }
            print($key . ': ' . $value . '<br>');
        }
    }
}
