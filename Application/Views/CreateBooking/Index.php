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
        // Waits for page to load, then makes functions available globally
        document.addEventListener("DOMContentLoaded", function () {
            window.getPreviousDate = function getPreviousDate(previousDate, location) {
                // Use AJAX to submit a PHP GET
                $.ajax({
                    type: "POST",
                    url: "../../Controllers/BookingController.php",
                    data: {
                        action: "previousDateWithLocation",
                        previousDate: previousDate,
                        location: location,
                    },
                    success: function (data) {
                        // Redirects so GET can post new date
                        const response = JSON.parse(data);
                        window.location.href = response.redirect;
                    }
                });
            }


            window.getNextDate = function getNextDate(nextDate, location) {
                // Use AJAX to submit a PHP GET
                $.ajax({
                    type: "POST",
                    url: "../../Controllers/BookingController.php",
                    data: {
                        action: "nextDateWithLocation",
                        nextDate: nextDate,
                        location: location,
                    },
                    success: function (data) {
                        // Redirects so GET can post new date
                        const response = JSON.parse(data);
                        window.location.href = response.redirect;
                    }
                });
            }


            window.removeBooking = function removeBooking(bookingTime, bookingLocation, bookingId) {
                // Confirmation dialog before removing the booking
                var result = confirm("Are you sure you want remove this booking?");

                // Use AJAX to call a PHP controller action
                if (result) {
                    $.ajax({
                        type: "POST",
                        url: "../../Controllers/BookingController.php",
                        data: {
                            action: "removeBooking",
                            bookingId: bookingId,
                        },
                        success: function (data) {
                            let response = JSON.parse(data);
                            if (response.message === "Successfully removed the booking.") {
                                // Revert the changes for a canceled booking
                                let timeslotElement = document.getElementById('timeslot-' + bookingTime);
                                if (timeslotElement) {
                                    timeslotElement.classList.remove('user-booked-timeslot');
                                    timeslotElement.classList.add('available-timeSlot');
                                    // Finds and replaces the "remove" button with "create" button
                                    let existingButton = timeslotElement.querySelector('.table-button');
                                    existingButton.setAttribute('onclick', `createTimeslotBooking(
                                        ${JSON.stringify(String(bookingTime))},
                                        ${JSON.stringify(String(bookingLocation))},
                                        ${JSON.stringify(null)}
                                    )`);
                                    existingButton.innerHTML = `
                                        <i class="book-icon fa-solid fa-circle-plus" aria-hidden="true"></i> Create
                                    `;
                                }
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log(jqXHR.responseText);
                        }
                    });
                }
            }


            window.createTimeslotBooking = function createTimeslotBooking(bookingTime, bookingLocation, bookingId) {
                // Use AJAX to call a PHP controller action
                $.ajax({
                    type: "POST",
                    url: "../../Controllers/BookingController.php",
                    data: {
                        action: "createBooking",
                        bookingTime: bookingTime,
                        bookingLocation: bookingLocation,
                    },
                    success: function (data) {
                        let response = JSON.parse(data);
                        if (response.message === "Successfully created the booking.") {
                            // Find the corresponding timeslot element and update its content and class
                            let timeslotElement = document.getElementById('timeslot-' + bookingTime);
                            if (timeslotElement) {
                                timeslotElement.classList.remove('available-timeSlot');
                                timeslotElement.classList.add('user-booked-timeslot');
                                // Finds and replaces the "create" button with "remove" button
                                let existingButton = timeslotElement.querySelector('.table-button');
                                existingButton.setAttribute('onclick', `removeBooking(
                                    ${JSON.stringify(String(bookingTime))},
                                    ${JSON.stringify(String(bookingLocation))},
                                    ${JSON.stringify(String(response.bookingId))}
                                )`);
                                existingButton.innerHTML = `
                                    <i class="remove-icon fa-solid fa-circle-xmark" aria-hidden="true"></i> Remove
                                `;
                            }
                        }
                    },
                    error: function (data) {
                        let response = JSON.parse(data);
                        alert(response.error);
                    }
                });

            }
        });
    </script>
</head>

<body>
<?php
    $isTutor = $_SESSION[SessionConst::USER_TYPE] == 'Tutor';
    Layout::displaySideMenu($isTutor);
?>

<div class="main-view">
        <h2>Set Your Tutoring Availability</h2>

        <div class="booking-date">
            <?php
            // Gets today's date (which cannot be earlier than today)
            $minDateValue = new DateTime();
            $startDate = (isset($_GET['date']) && (new DateTime($_GET['date']) >= new DateTime($minDateValue->format('d-m-Y'))) ) ? new DateTime($_GET['date']) : new DateTime();
            $dateValue = date('Y-m-d', $startDate->getTimestamp());

            // Dates for forward and backward date selection with arrows
            $previousDate = (new DateTime($dateValue))->modify('-1 day')->format('Y-m-d');
            $nextDate = (new DateTime($dateValue))->modify('+1 day')->format('Y-m-d');

            // Sets location for the new bookings the tutor creates
            $validLocations = ['Digital', 'UiA'];
            $bookingsLocation = (isset($_GET['location']) && ($_GET['location']) && in_array($_GET['location'], $validLocations)) ? $_GET['location'] : 'Digital';

            // Date selection form
            echo "
                <form class='booking-date-form' method='GET' action=''>
                        <i class='left-arrow fa-solid fa-angles-left' onclick='getPreviousDate(\"$previousDate\", \"$bookingsLocation\")'></i>
                        <input class='input-calendar' type='date' name='date' value='" . $dateValue . "'>
                        <i class='right-arrow fa-solid fa-angles-right' onclick='getNextDate(\"$nextDate\", \"$bookingsLocation\")'></i>
                        <input class='calendar-submit' type='submit' value='Check Date'>
                </form>
            ";
            // Location selection form
            echo "
                <div class='location-dropdown'>
                    <button class='location-dropbtn'>
                        Location: <u>$bookingsLocation</u> <i class='fa-solid fa-chevron-down'></i>
                    </button>
                    <div class='location-dropdown-content'>
                        <a href='?date=" . $dateValue . "&location=Digital'>Digital</a>
                        <a href='?date=" . $dateValue . "&location=UiA'>UiA</a>
                    </div>
                </div>
            ";
            ?>
        </div>

        <table class='calendar'>
            <?php
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
                $timeSlotEnd = DateTime::createFromFormat('H:i', $timeSlot)->modify('+15 minutes')->format('H:i');

                // The timeslot is available
                if ($booking == null) {
                    // Creates array with info to put as parameter into the ajax function 'createBooking'
                    $bookingTime = $startDate->format('d-m-Y ') . $timeSlot;
                    $encodedBookingArray = json_encode([
                        $bookingTime,
                        $bookingsLocation,
                        null,
                    ]);

                    echo "
                            <td class='available-timeSlot' id='timeslot-$bookingTime'>
                                <i class='clock-icon fa-regular fa-clock'></i> $timeSlot-$timeSlotEnd
                                <br>
                                <i class='location-icon fa-regular fa-location-dot'></i> <i>$bookingsLocation</i>
                                <br>
                                <i class='fa-solid fa-user'></i> None
                                <button class='table-button right-button' onclick='createTimeslotBooking(\"$bookingTime\", \"$bookingsLocation\", null)'>
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
                    $bookingLocation = $booking->getLocation();
                    $bookingTime = $startDate->format('d-m-Y ') . $timeSlot;
                    $encodedBookingArray = json_encode([
                        $bookingTime,
                        $bookingLocation,
                        $booking->getBookingId(),
                    ]);
                    echo "
                            <td class='user-booked-timeslot'  id='timeslot-$bookingTime'>
                                    <i class='clock-icon fa-regular fa-clock'></i> $timeSlot-$timeSlotEnd
                                    <br>
                                    <i class='location-icon fa-regular fa-location-dot'></i> <i> $bookingLocation</i>
                                    <br>
                                    <i class='fa-solid fa-user'></i> $studentName
                                    <button class='table-button right-button' onclick='removeBooking(\"$bookingTime\", \"$bookingsLocation\", \"{$booking->getBookingId()}\")'>
                                        <i class='remove-icon fa-solid fa-circle-xmark'></i> Remove
                                    </button>
                                
                            </td>
                    ";
                }

                echo "</tr>";
            }


            ?>
        </table>

    </div>


</body>

