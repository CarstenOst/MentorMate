<?php

class Validator
{
    /**
     * This method is made for Module 6 task 5.
     * validate either an email, password or a phone number
     *
     * @param string $type must be string of one of these: text | email | password | phone
     * @param string $value The string to check if is valid
     * @return bool True if valid, false if not
     * @throws Exception Will throw an exception if type entered is wrong.
     */
    public static function isValid(string $type, string $value): bool
    {
        return match ($type) {
            'text' => $value != "" && !ctype_space($value) && preg_match("/[a-zA-Z]/", $value),
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            'password' => self::validatePassword($value),
            'phone' => preg_match("/^[0-9]{8}$/", $value),
            default => throw new Exception('Invalid validation type specified'),
        };
    }

    /**
     * Checks if the password is valid
     * @param string $password The password to check
     * @return bool true if the password is valid, false if the password is invalid
     */
    private static function validatePassword(string $password): bool {
        return strlen($password) >= 9 &&                    // Longer than, or 9 characters
            preg_match('/[0-9]/', $password) &&     // Has one or more numbers
            preg_match('/[A-Z]/', $password) &&     // Has one or more upper case letters
            preg_match('/[a-z]/', $password) &&     // has one or more lower case letters
            preg_match('/[^a-zA-Z0-9]/', $password); // Has one or more special characters
    }

}