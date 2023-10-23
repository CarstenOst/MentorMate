<?php

namespace  Application\Views\User;

use Application\Views\Shared\Functions\HtmlRenderer;

const FIRST_NAME_COOKIE = 'First name';
const LAST_NAME_COOKIE = 'Last name';
const EMAIL_COOKIE = 'Email';
const USER_TYPE_COOKIE = 'User type';

const ABOUT_USER_COOKIE = 'About user';
const INPUT_FIELDS = [
    FIRST_NAME_COOKIE => 'Enter your first name*',
    LAST_NAME_COOKIE => 'Enter your last name*',
    EMAIL_COOKIE => 'Enter your email*',
    USER_TYPE_COOKIE => 'Enter "Student" or "LA"*',
    ABOUT_USER_COOKIE => 'Enter something about yourself',
];

HtmlRenderer::renderFormArrayBased(array_keys(INPUT_FIELDS), INPUT_FIELDS);