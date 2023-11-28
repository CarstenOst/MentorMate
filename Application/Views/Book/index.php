<?php
require("../../../autoloader.php");

use Application\Constants\SessionConst;
use Application\Validators\Auth;
use Application\Views\Shared\Layout;
use Infrastructure\Repositories\BookingRepository;
use Infrastructure\Repositories\UserRepository;
use Core\Entities\Booking;


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
                    url: "./BookController.php",
                    data: {
                        action: "cancelBooking",
                        bookingId: bookingId,
                    },
                });
            }
        }


        function bookTimeslot(bookingId, studentId) {
            // Use AJAX to call a PHP controller action
            $.ajax({
                type: "POST",
                url: "./BookController.php",
                data: {
                    action: "bookBooking",
                    bookingId: bookingId,
                    studentId: studentId,
                },
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

        <div class="booking-view">
            <!-- TODO update/style this title to better describe the page -->
            <h2>Book a timeslot from a Tutor</h2>

            <div class="booking-date">
                <?php
                // Gets today's date (which cannot be earlier than today)
                $minDateValue = new DateTime();
                $date = (isset($_GET['date']) && (new DateTime($_GET['date']) >= new DateTime($minDateValue->format('d-m-Y'))) ) ? new DateTime($_GET['date']) : new DateTime();
                $dateValue = (isset($_GET['date']) && (new DateTime($_GET['date']) >= new DateTime($minDateValue->format('d-m-Y'))) ) ? $_GET['date'] : $date->format('d-m-Y');

                echo "
                        <form class='booking-date-form' method='GET' action=''>
                                <input class='input-calendar' type='date' name='date' value='$dateValue'>
                                <input class='calendar-submit' type='submit' value='Check Date'>
                        </form>
                    ";
                ?>
            </div>

            <form method='POST' action=''>
                <table class='calendar'>
                    <?php
                    // Queries database for bookings for hour interval 08-23
                    $bookings = BookingRepository::getBookingForDate($date);
                    if (sizeof($bookings) == 0) {
                        echo "
                            <tr>
                                <th>Seems like there are no available Timeslots for {$date->format('d-m-Y')}...</th>
                            </tr>
                        ";
                    }


                    // Iterates over results to get header TutorId's
                    $tutors = [];
                    foreach ($bookings as $booking) {
                        $tutors[$booking->getTutorId()] =  UserRepository::read($booking->getTutorId())->getFirstName();
                    }
                    echo "<tr>";
                    foreach ($tutors as $tutorId => $tutorFirstName) {
                        echo "<th>$tutorFirstName</th>";
                    }
                    echo "</tr>";



                    // Iterates over bookings and puts arrays containing timeslots at array index using:
                    //    bookingTime and TutorId
                    $timeSlots = [];
                    foreach ($bookings as $booking) {
                        foreach ($tutors as $tutorId => $tutorFirstName) {
                            // appends "row" for each tutor, if no booking there, it will remain 'null'
                            $timeSlots[$booking->getBookingTime()->format('H:i')][$tutorId] = null;
                        }
                        // Replaces 'null' with 'booking' for indexes where the tutor has a booking
                        $timeSlots[$booking->getBookingTime()->format('H:i')][$booking->getTutorId()] = $booking;
                    }


                    // Creates table with timeslots
                    foreach ($timeSlots as $timeSlot => $tutorIds) {
                        foreach ($tutorIds as $tutorId => $booking) {
                            // The tutor has no booking for this 'timeSlot'
                            if ($booking == null) {
                                echo "<td class='no-booking-cell'></td>";
                            }

                            // Is booked by logged inn user
                            elseif ($booking->getStudentId() == $_SESSION[SessionConst::USER_ID]) {
                                echo "
                                    <td class='user-booked-timeslot'>
                                        $timeSlot
                                        <button class='table-button' onclick='confirmCancelation({$booking->getBookingId()})'><i class='cancel-icon fa-solid fa-ban'></i> Cancel</button>
                                    </td>";
                            }

                            // Is booked by other user
                            elseif ($booking->getStudentId()) {
                                echo "<td class='unavailable-timeslot'>$timeSlot</td>";
                            }

                            // Otherwise the booking should be available
                            else {
                                echo "
                                    <td class='available-timeSlot'>
                                        $timeSlot 
                                        <button class='table-button' onclick='bookTimeslot({$booking->getBookingId()}, {$_SESSION[SessionConst::USER_ID]})''>
                                            <i class='book-icon fa-solid fa-circle-plus'></i> Book
                                        </button>
                                    </td>";
                            }

                        }
                        echo "</tr>";
                    }


                    ?>
                </table>
            </form>

        </div>


</div>

</body>