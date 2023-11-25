<?php

namespace Application\Views\User;

require ("../../../autoloader.php");

use Application\Validators\Auth;
use Application\Views\Shared\Layout;
use Application\Constants\SessionConst;

if (!Auth::checkAuth()) {// Starts session, and checks if user is logged in. If not, redirects to login page
    header('Location: ./Login.php');
    exit();
}

class Profile
{
    /**
     * View the user profile if the user is logged-in
     * Session must be set!
     *
     * @return void echos the user profile
     */
    public static function viewUserProfile(): void
    {
        $firstName = $_SESSION[SessionConst::FIRST_NAME];
        $lastName = $_SESSION[SessionConst::LAST_NAME];
        $userType = $_SESSION[SessionConst::USER_TYPE];
        $email = $_SESSION[SessionConst::EMAIL];
        $about = $_SESSION[SessionConst::ABOUT];
        echo "
            <div class='user-profile'>
                <h5>$firstName $lastName</h5>
                <p><small>$userType</small></p>
                <p>Email: $email</p>
                <p>Bio: $about</p>
            </div>
        ";
    }
}
?>

// TODO remove this
SessionConst::sessionDebugger();
<head>
    <link rel="stylesheet" href="/Assets/style.css">
    <script src="https://kit.fontawesome.com/5928831ae4.js" crossorigin="anonymous"></script>
</head>

<body>
<div class="side-menu">
    <ul>
        <li><a class="logo-title" href="#">
                MentorMate
            </a>
        </li>
        <li>
            <a href="../../Views/User/Profile.php" class="side-menu-profile-link">
                <div class="profile">
                    <i class="profile-icon fa-solid fa-user"></i>
                    <p>Profile</p>
                </div>
            </a>
        </li>
        <li><a href="../Book/index.php">Book</a></li>
        <li><a href="../Bookings/index.php">Bookings</a></li>
        <li><a href="#">Messages</a></li>
        <li><a href="index.php?logout=1">Log Out</a></li>
    </ul>
</div>

<div class="main-view">
<?php
    Profile::viewUserProfile();
?>
</div>
