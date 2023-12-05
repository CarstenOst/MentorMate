<?php
namespace  Application\Views\User;


use Application\Constants\SessionConst;
use Application\Validators\Auth;

require("../../autoloader.php");

// Starts session, and checks if user is logged in. If not, redirects to login page
if (!Auth::checkAuth()) {
    header('Location: ./User/Login.php');
    exit();
}

$isTutor = $_SESSION[SessionConst::USER_TYPE] === 'Tutor';

?>
<head>
    <link rel="stylesheet" href="/Assets/style.css">
    <script src="https://kit.fontawesome.com/5928831ae4.js" crossorigin="anonymous"></script>
</head>

<body>

    <div class='custom-container'>
        <div>
            <div class="icon-container">
                <h2>MentorMate</h2>
                <h4>What do you want to do?</h4>
                <br>

                <?php
                    if ($isTutor) {
                        echo "
                            <a href='../Views/CreateBooking/index.php' class='custom-img-link'><img width='160' height='160' src='../Assets/create_booking.svg' alt='Create booking sessions'></a>
                            <a href='../Views/Bookings/index.php' class='custom-img-link'><img width='160' height='160' src='../Assets/bookings.svg' alt='Your booked sessions'></a>
                            <a href='../Views/AvailableTutors/index.php' class='custom-img-link'><img width='160' height='160' src='../Assets/available_tutors.svg' alt='Show available Tutors'></a>
                            <a href='../Views/Students/index.php' class='custom-img-link'><img width='160' height='160' src='../Assets/students.svg' alt='Students'></a>                            
                            <a href='../Views/Messages/index.php' class='custom-img-link'><img width='160' height='160' src='../Assets/messages.svg' alt='Your messages'></a>
                            <a href='../Views/User/Profile.php' class='custom-img-link'><img width='160' height='160' src='../Assets/profile.svg' alt='Your profile'></a>
                        ";
                    } else {
                        echo "
                            <a href='../Views/Book/index.php' class='custom-img-link'><img width='160' height='160' src='../Assets/book.svg' alt='Book session'></a>
                            <a href='../Views/Bookings/index.php' class='custom-img-link'><img width='160' height='160' src='../Assets/bookings.svg' alt='Your booked sessions'></a>
                            <a href='../Views/AvailableTutors/index.php' class='custom-img-link'><img width='160' height='160' src='../Assets/available_tutors.svg' alt='Show available Tutors'></a>
                            <a href='../Views/Students/index.php' class='custom-img-link'><img width='160' height='160' src='../Assets/students.svg' alt='Students'></a>                            
                            <a href='../Views/Messages/index.php' class='custom-img-link'><img width='160' height='160' src='../Assets/messages.svg' alt='Your messages'></a>
                            <a href='../Views/User/Profile.php' class='custom-img-link'><img width='160' height='160' src='../Assets/profile.svg' alt='Your profile'></a>
                        ";
                    }
                ?>
            </div>
        </div>
    </div>

</body>
