<?php

namespace Application\Views\User;

require ("../../../autoloader.php");

use DateTime;
use Application\Validators\Auth;
use Application\Views\Shared\Layout;
use Application\Constants\SessionConst;
use Infrastructure\Repositories\BookingRepository;
use Infrastructure\Repositories\UserRepository;

// Starts session, and checks if user is logged in. If not, redirects to login page
if (!Auth::checkAuth()) {
    header('Location: ./Login.php');
    exit();
}

// Check if the logout action is requested
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    // Call the logOut function from your class
    Auth::logOut();

    // Redirect to login page after logout
    header('Location: ../User/Login.php');
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
        $about = $_SESSION[SessionConst::ABOUT] === '' ? 'Bio in progress...' : $_SESSION[SessionConst::ABOUT];
        echo "
            <div class='profile-container'>
                <img src='../../Assets/profile.svg' alt='Tutors Profile Picture'>
                <h1 class='tutor-name'>$firstName $lastName</h1>
                <p class='user-type'>$userType</p>
        
                <div class='about'>
                    <h2>About $firstName</h2>
                    <i>$about</i>
                </div>
        
                <div class='contact-info'>
                    <h2>Contact Information</h2>
                    <p><b>Email:</b> $email</p>
                    <!-- <button class='message-button'>Message $userType</button> -->
                </div>
        ";
    }

}
?>

<head>
    <link rel="stylesheet" href="/Assets/style.css">
    <script src="https://kit.fontawesome.com/5928831ae4.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php
        $isTutor = $_SESSION[SessionConst::USER_TYPE] == 'Tutor';
        Layout::displaySideMenu($isTutor);
    ?>

    <div class="main-view">
        <?php
            Profile::viewUserProfile();
        ?>
    </div>
</body>
