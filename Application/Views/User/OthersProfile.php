<?php

namespace Views\User\OthersProfile;

require ("../../../autoloader.php");

use DateTime;
use Application\Constants\SessionConst;
use Application\Validators\Auth;
use Application\Views\Shared\Layout;
use Infrastructure\Repositories\BookingRepository;
use Infrastructure\Repositories\UserRepository;



// Starts session, and checks if user is logged in. If not, redirects to login page
if (!Auth::checkAuth()) {
    header('Location: ./Login.php');
    exit();
}

// Check if post request for a user is valid action is requested
if (isset($_POST['userId']) && $_POST['userId'] != null) {
    header('Location: ../User/Profile.php');
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


class OthersProfile
{
    public static function viewTutorProfile(int $userId): void
    {
        $user = UserRepository::read($userId);
        $firstName = $user->getFirstName();
        $lastName = $user->getLastName();
        $userType = $user->getUserType();
        $email = $user->getEmail();
        $about = $user->getAbout() === '' ? 'Bio in progress...' : $user->getAbout();

        // Displays upper half of profile section
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
                
        
                <div class='availability'>
                    <h2>Availability</h2>
                    
                    <div class='container-availability-table'>
                        <form method='POST' action=''>
                            <table class='calendar'> 
        ";

        // Puts table with tutor's available bookings under "Availability"
        $date = new DateTime();
        $bookings = BookingRepository::getTutorBookings($date, $userId);
        usort($bookings, function($a, $b) {
            return $a->getBookingTime() <=> $b->getBookingTime();
        });

        // Displays standard view if no upcoming bookings found, otherwise populates table with upcoming bookings
        if (sizeof($bookings) == 0) {
            echo "
                <tr>
                    <th><i>Seems like they have no available bookings...</i></th>
                </tr>
            ";
        } else {
            $uniqueDates = [];
            foreach ($bookings as $booking) {
                $uniqueDates[$booking->getBookingTime()->format('d-m-Y')] = 1;
            }


            // Populates table with booking rows
            foreach ($bookings as $booking) {
                // Shows row with sticky date header for each unique date
                $bookingDate = $booking->getBookingTime()->format('d-m-Y');
                if (array_key_exists($bookingDate, $uniqueDates)) {
                    echo "
                        <tr>
                            <th>{$booking->getBookingTime()->format('d-m-Y (l)')}</th>
                        </tr>
                    ";
                    unset($uniqueDates[$bookingDate]);
                }


                // Booking is available
                if ($booking->getStudentId() == null) {
                    echo "
                        <tr>
                            <td class='available-timeSlot'>
                                <i class='clock-icon fa-regular fa-clock'></i> {$booking->getBookingTime()->format('H:i')}
                                <button class='table-button right-button' onclick='bookTimeslot({$booking->getBookingId()})''>
                                    <i class='book-icon fa-solid fa-circle-plus'></i> Book
                                </button>
                                <br>
                                <i class='location-icon fa-regular fa-location-dot'></i> <i>{$booking->getLocation()}</i>
                            </td>
                        </tr>
                    ";
                }

                // Is booked by logged inn user
                elseif ($booking->getStudentId() == $_SESSION[SessionConst::USER_ID]) {
                    echo "
                        <tr>
                            <td class='user-booked-timeslot'>
                                <i class='clock-icon fa-regular fa-clock'></i> {$booking->getBookingTime()->format('H:i')}
                                <button class='table-button right-button' onclick='confirmCancellation({$booking->getBookingId()})'>
                                    <i class='cancel-icon fa-solid fa-ban'></i> Cancel
                                </button>
                                <br>
                                <i class='location-icon fa-regular fa-location-dot'></i> <i> {$booking->getLocation()}</i>
                            </td>
                        </tr>

                    ";
                }
            }

        }
        // Finishes form and "Availability" section
        echo "
                            </table>
                        </form>
                    </div>
            
                </div>
            </div>
        ";
    }



    public static function viewStudentProfile(int $userId): void
    {
        $user = UserRepository::read($userId);
        $firstName = $user->getFirstName();
        $lastName = $user->getLastName();
        $userType = $user->getUserType();
        $email = $user->getEmail();
        $about = $user->getAbout() === '' ? 'Bio in progress...' : $user->getAbout();

        // Displays upper half of profile section
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
            </div>
        ";
    }
}

?>



<head lang="en">
    <link rel="stylesheet" href="../../Assets/style.css">
    <script src="https://kit.fontawesome.com/5928831ae4.js" crossorigin="anonymous"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        function confirmCancellation(bookingId) {
            // Confirmation dialog before cancelling
            let result = confirm("Are you sure you want cancel this booking?");

            // Use AJAX to call a PHP controller action
            if (result) {
                $.ajax({
                    type: "POST",
                    url: "../../Controllers/BookingController.php",
                    data: {
                        action: "cancelBooking",
                        bookingId: bookingId,
                    },
                    error: function(data) {
                        let response = JSON.parse(data);
                        alert(response.error);
                    }
                });
            }
        }


        function bookTimeslot(bookingId) {
            // Use AJAX to call a PHP controller action
            $.ajax({
                type: "POST",
                url: "../../Controllers/BookingController.php",
                data: {
                    action: "bookBooking",
                    bookingId: bookingId,
                },
                error: function(data) {
                    let response = JSON.parse(data);
                    alert(response.error);
                }
            });
        }
    </script>
</head>

<body>
    <?php
        $isTutor = $_SESSION[SessionConst::USER_TYPE] == 'Tutor';
        Layout::displaySideMenu($isTutor);
    ?>

    <div class="main-view">
        <?php
            $userId = $_SESSION['last_viewed_profile'];
            if ($isTutor) {
                OthersProfile::viewStudentProfile($userId);
            } else {
                OthersProfile::viewTutorProfile($userId);
            }
        ?>
    </div>
</body>
