<?php
require("../../../autoloader.php");

use Application\Constants\SessionConst;
use Application\Validators\Auth;
use Application\Views\Shared\Layout;
use Infrastructure\Repositories\BookingRepository;
use Infrastructure\Repositories\UserRepository;


// Starts session, and checks if user is logged in. If not, redirects to login page
if (!Auth::checkAuth()) {
    header('Location: ../User/Login.php');
    exit();
}

if ($_SESSION[SessionConst::USER_TYPE] !== 'Tutor') {
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



<html>
<head>
    <link rel="stylesheet" href="/Assets/style.css">
    <script src="https://kit.fontawesome.com/5928831ae4.js" crossorigin="anonymous"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        function removeBooking(bookingId) {
            // Confirmation dialog before removing the booking
            var result = confirm("Are you sure you want remove this booking?");

            // Use AJAX to call a PHP controller action
            if (result) {
                $.ajax({
                    type: "POST",
                    url: "./CreateBookingController.php",
                    data: {
                        action: "removeBooking",
                        bookingId: bookingId,
                    },
                });
            }
        }


        function createTimeslotBooking(encodedBookingArray) {
            // Extract values from the array
            var tutorId = encodedBookingArray[0];
            var bookingTime = encodedBookingArray[1];
            var bookingLocation = encodedBookingArray[2];

            // Use AJAX to call a PHP controller action
            $.ajax({
                type: "POST",
                url: "./CreateBookingController.php",
                data: {
                    action: "createBooking",
                    tutorId: tutorId,
                    bookingTime: bookingTime,
                    bookingLocation: bookingLocation,
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
        <h2>Create bookings for when you can tutor</h2>

        <div class="booking-date">
            <?php
            // Gets today's date (which cannot be earlier than today)
            $minDateValue = new DateTime();
            $startDate = (isset($_GET['date']) && (new DateTime($_GET['date']) >= new DateTime($minDateValue->format('d-m-Y'))) ) ? new DateTime($_GET['date']) : new DateTime();
            $dateValue = (isset($_GET['date']) && (new DateTime($_GET['date']) >= new DateTime($minDateValue->format('d-m-Y'))) ) ? $_GET['date'] : $startDate->format('d-m-Y');

            // Sets location for the new bookings the tutor creates
            $bookingsLocation = (isset($_GET['location']) && ($_GET['location'])) ? $_GET['location'] : 'Digital';
            echo "
                        <form class='booking-date-form' method='GET' action=''>
                                <input class='input-calendar' type='date' name='date' value='$dateValue'>
                                <input class='calendar-submit' type='submit' value='Check Date'>
                        </form>
                    ";

            echo "
                        <form class='booking-location-form' method='GET' action=''>
                                <input class='input-location' type='text' name='location' placeholder='Set the booking location'> 
                                <!-- <select class='tutor-input-location' name='location'>
                                    <option>Digital</option>
                                </select> -->
                                <input class='location-submit' type='submit' value='Set Location'>
                        </form>
                    ";
            ?>
        </div>

        <form method='POST' action=''>
            <table class='calendar'>
                <?php
                // Start and end dates for timeSlots (startDate is set in form above)
                $endDate = DateTime::createFromFormat('d-m-Y H:i:s', $startDate->format('d-m-Y H:i:s'));

                // Creates DateTime variables for timeSlot intervals as keys
                $startHourMinute = DateTime::createFromFormat('d-m-Y H:i:s' , $startDate->format('d-m-Y') . ' 08:00:00');
                $endHourMinute = DateTime::createFromFormat('d-m-Y H:i:s', $startDate->format('d-m-Y') . ' 23:59:59');

                $timeSlots = [];

                // Queries database for bookings for hour interval '08-23'
                $queriedBookings = BookingRepository::getTutorBookingsForDate($startDate, $_SESSION[SessionConst::USER_ID]);
                $existingTutorBookings = [];
                foreach ($queriedBookings as $booking) {
                    $existingTutorBookings[$booking->getBookingTime()->format('H:i')] = $booking;
                }

                // Populates associative array with 'booking' or null if none exists for that time slot
                while ($startHourMinute < $endHourMinute) {
                    $days = DateTime::createFromFormat('d-m-Y H:i:s' , $startDate->format('d-m-Y'));
                    $currentTimeSlot = $startHourMinute->format('H:i');

                    // Sets timeslots for each 15-minute interval from 08:00 to 23:45 to be 'booking' or null if the tutor does not have one existing
                    $timeSlotBooking = array_key_exists($currentTimeSlot, $existingTutorBookings) ? $existingTutorBookings[$currentTimeSlot] : null;
                    $timeSlots[$currentTimeSlot] = $timeSlotBooking;

                    // Increment time by 15 minutes for next timeSlot
                    $startHourMinute->modify('+15 minutes');
                }

                // Creates table headers with dates
                echo "
                    <tr>
                        <th>{$startDate->format('d-m-Y (l)')}</th>
                    </tr>
                ";


                // Creates table with free timeslots (or existing 'booking')
                foreach ($timeSlots as $timeSlot => $booking) {

                    // The timeslot is available
                    if ($booking == null) {
                        // Creates array with info to put as parameter into the ajax function 'createBooking'
                        $bookingTime = $startDate->format('d-m-Y ') . $timeSlot;
                        $encodedBookingArray = json_encode([
                            $bookingTime,
                            $bookingsLocation
                        ]);

                        echo "
                                <td class='available-timeSlot'>
                                    <i class='clock-icon fa-regular fa-clock'></i> $timeSlot
                                    <br>
                                    <i class='location-icon fa-regular fa-location-dot'></i> <i>$bookingsLocation</i>
                                    <br>
                                    <i class='fa-solid fa-user'></i> None
                                    <button class='table-button right-button' onclick='createTimeslotBooking($encodedBookingArray)'>
                                        <i class='book-icon fa-solid fa-circle-plus'></i> Create
                                    </button>
                                </td>
                        ";
                    }

                    // The time slot already has a booking created by the tutor
                    elseif ($booking->getTutorId() == $_SESSION[SessionConst::USER_ID]) {
                        // Fetches the booking info
                        $bookingId = $booking->getBookingId();
                        $studentId = $booking->getStudentId();
                        $studentName = is_string(UserRepository::read($studentId)) ? 'None' : UserRepository::read($studentId)->getFirstName();
                        $bookingLocation = $booking->getStatus();

                        echo "
                                <td class='user-booked-timeslot'>
                                        <i class='clock-icon fa-regular fa-clock'></i> {$booking->getBookingTime()->format('H:i')}
                                        <br>
                                        <i class='location-icon fa-regular fa-location-dot'></i> <i> $bookingLocation</i>
                                        <br>
                                        <i class='fa-solid fa-user'></i> $studentName
                                        <button class='table-button right-button' onclick='removeBooking($bookingId)'>
                                            <i class='remove-icon fa-solid fa-circle-xmark'></i> Remove
                                        </button>
                                    
                                </td>
                        ";
                    }

                    echo "</tr>";
                }


                ?>
            </table>
        </form>

    </div>


</div>

</body>

