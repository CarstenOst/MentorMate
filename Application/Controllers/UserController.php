<?php

namespace Application\Controllers\UserController;

require("../../autoloader.php");

use Core\Entities\User;
use DateTime;
use Exception;
use Application\Constants\SessionConst;
use Application\Validators\Auth;


// Starts session, and checks if user is logged in. If not, redirects to login page
if (!Auth::checkAuth()) {
    header('Location: ../User/Login.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        // Actions for
        case 'case1':
            break;

    }

    exit();
}
