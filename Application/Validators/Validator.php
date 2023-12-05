<?php

namespace Application\Validators;

class Validator
{
    public const TEXT = 'text';
    public const EMAIL = 'email';
    public const PASSWORD = 'password';
    public const USER_TYPE = 'userType';
    public const PHONE_NUMBER = 'phoneNumber';
    public const VALID_USER_TYPES = ['student', 'tutor'];


    /**
     * This function was made for Module 6, task 5 to validate
     * either an email, password or a phone number in the same function.
     *
     * @param string $type must be string of one of these: text | email | password | phone.
     * @param string $value The string to check if is valid.
     * @return bool True if valid, false if not.
     */
    public static function isValid(string $type, string $value): bool
    {
        return match ($type) {
            self::TEXT => $value != "" && !ctype_space($value) && preg_match("/[a-zæøåA-ZÆØÅ]/", $value),
            self::EMAIL => self::validEmailInput($value),
            self::PASSWORD => self::isPassword($value),
            self::USER_TYPE => self::isUserType($value),
            self::PHONE_NUMBER => preg_match("/^[0-9]{8}$/", $value),
            default => false,
        };
    }

    /**
     * Checks if the email is valid.
     *
     * @param string $original_email The email to check.
     * @return bool true if the email is valid, false if the email is invalid.
     */
    private static function validEmailInput(string $original_email): bool
    {
        $clean_email = filter_var($original_email,FILTER_SANITIZE_EMAIL);

        // If the email is the same after sanitizing, and it is a valid email, return true
        if ($original_email == $clean_email && filter_var($original_email,FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        // Else the user might have typed wrong, so do not upload it to the database, even if it is valid.
        // This email won't be accepted "test<?php echo 'test';?\>@gmail.com"
        return false;
    }

    /**
     * Checks if the usertype exists
     *
     * @param string $userType
     * @return bool true if usertype/role exists
     */
    private static function isUserType(string $userType): bool
    {
        return in_array(strtolower($userType), self::VALID_USER_TYPES, true);
    }

    /**
     * Checks if the password is valid.
     *
     * @param string $password The password to check.
     * @return bool true if the password is valid, false if the password is invalid.
     * TODO make this return an array to give a message of what failed to the user
     */
    private static function isPassword(string $password): bool
    {
        return strlen($password) >= 9 &&                    // Longer than, or 9 characters.
            preg_match('/[0-9]/', $password) &&     // Has one or more numbers.
            preg_match('/[A-Z]/', $password) &&     // Has one or more upper case letters.
            preg_match('/[a-z]/', $password) &&     // Has one or more lower case letters.
            preg_match('/[^a-zA-Z0-9]/', $password);// Has one or more special characters.
    }

    /**
     * Checks if string has no special characters and only letters (numbers are discarded too).
     *
     * @param string $str The string to check for special characters.
     * @return bool true if string is only letters, false if not.
     */
    public static function hasNoSpecialCharacters(string $str): bool
    {
        // Type cast to bool is here redundant, as the not operator is used, forcing the return value to be bool.
        return !preg_match('/[^A-ZÆØÅ ]/iu', $str);
    }

    /**
     * Removes whitespace from string.
     *
     * @param string $str The string to remove whitespace from.
     * @return string The string without whitespace.
     */
    public static function removeWhiteSpace(string $str): string
    {
        return preg_replace('/\s+/', '', $str);
    }


    /**
     * Warning reference is used here. The error message is added to the array given in the parameter.
     *
     * @param string $name The name to validate.
     * @param array $errorMessage Reference to the array where the error message should be added.
     * @return bool true if name is valid, false if not.
     */
    public static function validateName(string $name, array &$errorMessage): bool
    {
        if (Validator::hasNoSpecialCharacters($name)) {
            return true;
        }
        $name = htmlspecialchars($name);
        $errorMessage[] = "Names can only contain letters. You typed in '$name'";
        return false;
    }


    /**
     * Function to validate an email.
     *
     * @param string $email The email to validate.
     * @param array $errorMessage Reference to the array where the error message should be added.
     * @return bool true if email is valid, false if not.
     */
    public static function validateEmail(string $email, array &$errorMessage): bool
    {
        if (Validator::isValid(Validator::EMAIL, $email)) {
            return true;
        }
        $email = htmlspecialchars($email);
        $errorMessage[] = "Email: '$email' is not valid";
        return false;
    }

    /**
     * Function to validate password.
     *
     * @param string $password The password to validate.
     * @param array $errorMessage Reference to the array where the error message should be added.
     * @return bool true if password is valid
     */
    public static function validatePassword(string $password, array &$errorMessage): bool
    {
        // Only checks valid format
        if (Validator::isValid(Validator::PASSWORD, $password)) {
            return true;
        }
        // TODO make error message better (so it only lists the missing requirements
        // TODO or spend more time on other stuff (:
        $errorMessage[] = "Password must be longer than 9 characters and contain 1 number, a special character, a capital and a lower case letter";
        return false;

    }


    /**
     * Function to validate user type.
     *
     * @param string $userTypeInput The user type to validate.
     * @param array $errorMessage Reference to the array where the error message should be added.
     * @return bool true if phone number is valid
     */
    public static function validateUserType(string $userTypeInput, array &$errorMessage): bool
    {
        if (Validator::isValid(Validator::USER_TYPE, strtolower($userTypeInput))) {
            return true;
        }
        $errorMessage[] = 'Must be one of these types: ' . implode(', ',Validator::VALID_USER_TYPES);
        return false;
    }

}
