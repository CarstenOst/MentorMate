<?php

namespace Application\Validators;

use Exception;

class Validator
{
    public const TEXT = 'text';
    public const EMAIL = 'email';
    public const PASSWORD = 'password';
    public const USER_TYPE = 'userType';
    public const PHONE_NUMBER = 'phoneNumber';

    /**
     * This function is made for Module 6, task 5 to validate
     * either an email, password or a phone number in the same function.
     *
     * @param string $type must be string of one of these: text | email | password | phone.
     * @param string $value The string to check if is valid.
     * @return bool True if valid, false if not.
     */
    public static function isValid(string $type, string $value): bool
    {
        return match ($type) {
            self::TEXT => $value != "" && !ctype_space($value) && preg_match("/[a-zA-Z]/", $value),
            self::EMAIL => filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            self::PASSWORD => self::validatePassword($value),
            self::USER_TYPE => self::validateUserType($value),
            self::PHONE_NUMBER => preg_match("/^[0-9]{8}$/", $value),
            default => false,
        };
    }

    private static function validateUserType($userType): bool
    {
        return match ($userType) {
            'student', 'tutor' => true,
            default => false
        };
    }

    /**
     * Checks if the password is valid.
     *
     * @param string $password The password to check.
     * @return bool true if the password is valid, false if the password is invalid.
     * TODO make this return an array to give a message of what failed to the user
     */
    private static function validatePassword(string $password): bool
    {
        return strlen($password) >= 9 &&                    // Longer than, or 9 characters.
            preg_match('/[0-9]/', $password) &&     // Has one or more numbers.
            preg_match('/[A-Z]/', $password) &&     // Has one or more upper case letters.
            preg_match('/[a-z]/', $password) &&     // Has one or more lower case letters.
            preg_match('/[^a-zA-Z0-9]/', $password);// Has one or more special characters.
    }

}
