<?php

namespace  Application\Views\User;

use Application\Views\Shared\Functions\HtmlRenderer;
use Application\Views\Shared\Layout;

const FIRST_NAME_COOKIE = 'FirstName';
const LAST_NAME_COOKIE = 'LastName';
const EMAIL_COOKIE = 'Email';
const USER_TYPE_COOKIE = 'UserType';
const ABOUT_USER_COOKIE = 'AboutUser';

const INPUT_FIELDS = [
    FIRST_NAME_COOKIE => 'Enter your first name*',
    LAST_NAME_COOKIE => 'Enter your last name*',
    EMAIL_COOKIE => 'Enter your email*',
    USER_TYPE_COOKIE => 'Enter "Student" or "LA"*',
    ABOUT_USER_COOKIE => 'Enter something about yourself',
];

Layout::displayTop();
HtmlRenderer::renderFormArrayBased(array_keys(INPUT_FIELDS), INPUT_FIELDS);
Layout::displayBottom();
