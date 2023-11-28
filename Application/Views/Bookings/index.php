<?php
require("../../../autoloader.php");

use Application\Constants\SessionConst;
use Application\Validators\Auth;
use Infrastructure\Repositories\BookingRepository;
use Infrastructure\Repositories\UserRepository;
use Core\Entities\Booking;
use Application\Views\Shared\Layout;


// Starts session, and checks if user is logged in. If not, redirects to login page
if (!Auth::checkAuth()) {
    header('Location: ../User/Login.php');
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

?>



<html>
<head>
    <link rel="stylesheet" href="/Assets/style.css">
    <script src="https://kit.fontawesome.com/5928831ae4.js" crossorigin="anonymous"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
            function confirmCancelation(bookingId) {
                // Confirmation dialog before cancelling
                var result = confirm("Are you sure you want cancel this booking?");

                // Use AJAX to call a PHP controller action
                if (result) {
                    $.ajax({
                        type: "POST",
                        url: "./BookingsController.php",
                        data: {
                            action: "cancelBooking",
                            bookingId: bookingId
                        },
                    });
                }
            }


            function messageTutor(tutorId) {
                // Use AJAX to call a PHP controller action
                $.ajax({
                    type: "POST",
                    url: "./BookingsController.php",
                    data: {
                        action: "messageTutor",
                        tutorId: tutorId
                    }
                });
            }

    </script>

</head>

<body>

<?php
    Layout::displaySideMenu();
?>


<div class="main-view">

    <div class="booking-view">
        <div>
            <h2>Your bookings</h2>
        </div>


        <form method='POST' action=''>
            <table class='calendar'>
                <?php

                // Queries database for bookings for hour interval 08-23
                $date = new DateTime();
                $bookings = BookingRepository::getStudentBookings($date, $_SESSION[SessionConst::USER_ID]);
                if (sizeof($bookings) == 0) {
                    echo "<br>Seems like you have no future bookings...";
                } else {
                    // Creates headers for table
                    $boookingHeaders = ["Booking date", "Location", "Tutor", "Cancel Timeslot", "Message", "Add to calendar"];
                    echo "<tr>";
                    foreach ($boookingHeaders as $header) {
                        echo "<th>$header</th>";
                    }
                    echo "</tr>";

                    // Populates table with booking rows
                    foreach ($bookings as $booking) {
                        $tutorId = $booking->getTutorId();
                        $tutorName = UserRepository::read($tutorId)->getFirstName();
                        $bookingId = $booking->getBookingId();
                        echo "
                            <tr>
                                <td>
                                    <i class='calendar-icon fa-regular fa-calendar'></i> {$booking->getBookingTime()->format('d-m-Y')}
                                    <br>
                                    <i class='clock-icon fa-regular fa-clock'></i> {$booking->getBookingTime()->format('H:i')}
                                </td>
                                <td>
                                    <i class='location-icon fa-regular fa-location-dot'></i> {$booking->getStatus()}
                                </td>
                                <td>
                                    <i class='fa-solid fa-user'></i> {$tutorName}
                                </td>
                                <td>
                                    <button class='table-button' onclick='confirmCancelation($bookingId)'><i class='cancel-icon fa-solid fa-ban'></i> Cancel</button>
                                </td>
                                <td>
                                    <button class='table-button' onclick='messageTutor($tutorId)'><i class='message-icon fa-solid fa-message'></i> Message</button>
                                </td>
                                <td>
                                    <button class='table-button'><i class='calendar-icon fa-regular fa-calendar-plus'></i> Add boooking</button>
                                </td>
                            </tr>
                        ";
                    }

                }

                ?>

            </table>
        </form>
    </div>


</div>

</body>