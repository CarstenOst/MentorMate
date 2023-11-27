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

// Checks if timeslots were booked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book'])) {
    // Check if 'timeslots' is set in $_POST
    if (isset($_POST['timeslots']) && is_array($_POST['timeslots'])) {
        // Loops through the tutorId's posted
        foreach ($_POST['timeslots'] as $tutorId => $timeSlots) {
            // Loops through 'checked off' timeslots that are json encoded associative arrays
            foreach ($timeSlots as $timeSlot => $bookingDataJson) {
                $bookingArrayDecoded = json_decode($bookingDataJson, true);
                $bookingTime = DateTime::createFromFormat('Y-m-d H:i:s', $timeSlot);

                $newBooking = new Booking();
                $newBooking->setBookingId($bookingArrayDecoded['bookingId']);
                $newBooking->setStudentId($bookingArrayDecoded['studentId']);
                $newBooking->setTutorId($bookingArrayDecoded['tutorId']);
                $newBooking->setBookingTime($bookingTime);
                $newBooking->setStatus($bookingArrayDecoded['status']);
                $newBooking->setCreatedAt(DateTime::createFromFormat('Y-m-d H:i', $bookingArrayDecoded['createdAt']));
                $newBooking->setUpdatedAt(DateTime::createFromFormat('Y-m-d H:i', $bookingArrayDecoded['updatedAt']));
                BookingRepository::update($newBooking);
            }
        }
    }
}
?>



<html>
<head>
    <link rel="stylesheet" href="/Assets/style.css">
    <script src="https://kit.fontawesome.com/5928831ae4.js" crossorigin="anonymous"></script>
</head>

<body>
<?php
    Layout::displaySideMenu();
?>

<div class="main-view">

    <div class="booking-view">
        <div class="booking-date">
            <?php
            // Gets today's date
            $date = new DateTime();
            $dateValue = isset($_GET['date']) ? $_GET['date'] : $date->format('d-m-Y');
            // Check if a new date is set in the URL
            if (isset($_GET['date'])) {
                $date = new DateTime($_GET['date']);
            }

            echo "
                        <form method='GET' action=''>
                            <div class='booking-date-form'>
                                <!-- TODO update this so the min valid date is 'date', so users cannot book retroactively -->
                                <input class='input-calendar' type='date' name='date' value='$dateValue'>
                                <input class='calendar-submit' type='submit' value='Check Date'>
                            </div>
                            
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
                    echo "<br>Seems like there are no Timeslots for {$date->format('d-m-Y')}...";
                }


                // Iterates over results to get header TutorId's
                $TAsHeader = [];
                foreach ($bookings as $booking) {
                    $TAsHeader[$booking->getTutorId()] = $booking->getTutorId();
                }
                echo "<tr>";
                foreach (array_keys($TAsHeader) as $key) {
                    $TAname = UserRepository::read($TAsHeader[$key])->getFirstName();
                    echo "<th>$TAname</th>";
                }
                echo "</tr>";



                // Iterates over bookings and puts arrays containing timeslots at array index using TutorId
                $timeSlots = [];
                foreach ($bookings as $booking) {
                    $timeSlots[$booking->getBookingTime()->format('H:i')][$booking->getTutorId()] = $booking;
                }

                // Creates table with timeslots
                foreach (array_keys($timeSlots) as $timeSlot) {
                    echo "<tr>";
                    foreach ($timeSlots[$timeSlot] as $booking) {
                        // Checks if the timeSlot has a studentId (meaning it's booked)
                        if ($booking->getStudentId()) {
                            // Is booked
                            echo "<td class='unavailable-timeslot'>$timeSlot</td>";
                        } else {
                            // Is available
                            // Combine date (set in date selection form) and timeSlot into singular string
                            $firstName = $_SESSION[SessionConst::FIRST_NAME];
                            $bookingTimeString = $date->format('Y-m-d') . ' ' . $booking->getBookingTime()->format('H:i:s');
                            $updatedDateTime = new DateTime();
                            $bookingArrayEncoded = json_encode([
                                'bookingId' => $booking->getBookingId(),
                                'studentId' => $_SESSION[SessionConst::USER_ID],
                                'tutorId' => $booking->getTutorId(),
                                // Don't need to have bookingTime
                                'status' => $booking->getStatus(),
                                'createdAt' => $booking->getCreatedAt()->format('Y-m-d H:i'),
                                'updatedAt' => $updatedDateTime->format('Y-m-d H:i'),
                            ]);
                            echo "<td class='available-timeSlot'><input class='book-checkbox' type='checkbox' name='timeslots[{$booking->getTutorId()}][$bookingTimeString]' value='{$bookingArrayEncoded}'>$timeSlot</td>";
                        }

                        // TODO add third table data option, where studentId is not null, but it's equal to the logged inn userId


                    }
                    echo "</tr>";
                }


                ?>

            </table>
            <input class="submit-button" type="submit" name="book" value="Book Timeslots">
        </form>
    </div>


</div>


</body>