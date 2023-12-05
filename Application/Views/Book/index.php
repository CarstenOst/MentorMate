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

if ($_SESSION[SessionConst::USER_TYPE] !== 'Student') {
    header('Location: ../User/Profile.php');
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



<html lang="en">
<head>
    <link rel="stylesheet" href="../../Assets/style.css">
    <script src="https://kit.fontawesome.com/5928831ae4.js" crossorigin="anonymous"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        function getPreviousDate(previousDate) {
            // Use AJAX to submit a PHP GET
            $.ajax({
                type: "POST",
                url: "../../Controllers/BookingController.php",
                data: {
                    action: "previousDate",
                    previousDate: previousDate,
                },
                success: function(data) {
                    // Redirects so GET can post new date
                    const response = JSON.parse(data);
                    window.location.href = response.redirect;
                }
            });
        }

        function getNextDate(nextDate) {
            // Use AJAX to submit a PHP GET
            $.ajax({
                type: "POST",
                url: "../../Controllers/BookingController.php",
                data: {
                    action: "nextDate",
                    nextDate: nextDate,
                },
                success: function(data) {
                    // Redirects so GET can post new date
                    const response = JSON.parse(data);
                    window.location.href = response.redirect;
                }
            });
        }


        function confirmCancelation(bookingId) {
            // Confirmation dialog before cancelling
            var result = confirm("Are you sure you want cancel this booking?");

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

    <div class="booking-view">
            <h2>Book a Tutoring Session</h2>

            <div class="booking-date">
                <?php
                // Gets today's date (which cannot be earlier than today)
                $minDateValue = new DateTime();
                $date = (isset($_GET['date']) && (new DateTime($_GET['date']) >= new DateTime($minDateValue->format('Y-m-d'))) ) ? new DateTime($_GET['date']) : new DateTime();
                $dateValue = date('Y-m-d', $date->getTimestamp());

                // Dates for forward and backward date selection with arrows
                $previousDate = (new DateTime($dateValue))->modify('-1 day')->format('Y-m-d');
                $nextDate = (new DateTime($dateValue))->modify('+1 day')->format('Y-m-d');

                echo "
                        <form class='booking-date-form' method='GET' action=''>
                                <i class='left-arrow fa-solid fa-angles-left' onclick='getPreviousDate(\"$previousDate\")'></i>
                                <input class='input-calendar' type='date' name='date' value='" . $dateValue . "'>
                                <i class='right-arrow fa-solid fa-angles-right' onclick='getNextDate(\"$nextDate\")'></i>
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
                                <th>Seems like there are no available sessions for {$date->format('d-m-Y (l)')}...</th>
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


                    // Iterates over bookings and puts arrays containing timeslots at array index using bookingTime and TutorId
                    $timeSlots = [];
                    foreach ($bookings as $booking) {
                        foreach ($tutors as $tutorId => $tutorFirstName) {
                            // appends "row" for each tutor, if no booking there, it will remain 'null'
                            $timeSlots[$booking->getBookingTime()->format('H:i')][$tutorId] = null;
                        }
                    }

                    // Inserts bookings into the datastructure where they are
                    foreach ($bookings as $booking) {
                        // Replaces 'null' with 'booking' for indexes where the tutor has a booking
                        $timeSlots[$booking->getBookingTime()->format('H:i')][$booking->getTutorId()] = $booking;
                    }
                    // Sorts timeSlots ascending
                    ksort($timeSlots);


                    // Creates table with timeslots
                    foreach ($timeSlots as $timeSlot => $tutorIds) {
                        echo "<tr>";
                        foreach ($tutorIds as $tutorId => $booking) {
                            // The tutor has no booking for this 'timeSlot'
                            if ($booking == null) {
                                echo "<td class='no-booking-cell'></td>";
                            }

                            // Is booked by logged inn user
                            elseif ($booking->getStudentId() == $_SESSION[SessionConst::USER_ID]) {
                                echo "
                                    <td class='user-booked-timeslot'>
                                        <i class='clock-icon fa-regular fa-clock'></i> {$booking->getBookingTime()->format('H:i')}
                                        <button class='table-button right-button' onclick='confirmCancelation({$booking->getBookingId()})'>
                                            <i class='cancel-icon fa-solid fa-ban'></i> Cancel
                                        </button>
                                        <br>
                                        <i class='location-icon fa-regular fa-location-dot'></i> <i> {$booking->getLocation()}</i>
                                        
                                    </td>";
                            }

                            // Is booked by other user
                            elseif ($booking->getStudentId()) {
                                echo "
                                    <td class='unavailable-timeslot'>
                                        <i class='clock-icon fa-regular fa-clock'></i> $timeSlot
                                    </td>";
                            }

                            // Otherwise the booking should be available
                            else {
                                echo "
                                    <td class='available-timeSlot'>
                                        <i class='clock-icon fa-regular fa-clock'></i> {$booking->getBookingTime()->format('H:i')}
                                        <button class='table-button right-button' onclick='bookTimeslot({$booking->getBookingId()})''>
                                            <i class='book-icon fa-solid fa-circle-plus'></i> Book
                                        </button>
                                        <br>
                                        <i class='location-icon fa-regular fa-location-dot'></i> <i>{$booking->getLocation()}</i>
                                        
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
