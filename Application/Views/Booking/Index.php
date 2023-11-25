<?php

require ("../../../autoloader.php");
use Core\Entities\Booking;
use Application\Constants\SessionConst;
use Application\Validators\Auth;
use Infrastructure\Repositories\BookingRepository;
use Infrastructure\Repositories\UserRepository;

// Starts session, and checks if user is logged in. If not, redirects to login page
if (!Auth::checkAuth()) {
    header('Location: ./Login.php');
    exit();
}


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Create an array to store the names of checked checkboxes
        $checkedCheckboxes = [];

        foreach ($_POST as $key => $value) {
            // Loop through the POST data

            if (strpos($key, '-') !== false && $value === 'on') {
                // Check if the key contains a hyphen (e.g., '30-10-2023-9:00') and the value is 'on'

                // Extract the name of the checkbox (e.g., '30-10-2023-9:00')
                $checkboxName = $key;

                // Add the checkbox name to the array
                $checkedCheckboxes[] = $checkboxName;
            }
        }
        $formData = $_POST;

        $formData['studentId'] = 0;
        $formData['tutorId'] = 6; // TODO Should be swapped to the logged inn tutorID, currently a fixed user
        $test = implode($formData);
        echo "$test";
        // Iterates over the submitted bookings, and submits them one by one
        for ($i = 0; $i < count($checkedCheckboxes); $i++) {
            $checkboxName = $formData[$i];
            $test = implode($formData[$checkboxName]);
            echo "$test";
            echo "success!";
            /*
            $booking = new Booking();
            $booking->setStudentId($formData['studentId']);
            $booking->setTutorId($formData['tutorId']);
            $booking->setBookingTime($formData['bookingTime']);
            $booking->setStatus($formData['status']);

            BookingRepository::create($booking);
            */
        }
    }

?>

<html>
<head>
    <link rel="stylesheet" href="/Assets/style.css">
</head>

<body>
<div class="side-menu">

</div>


<div class="main-view">

    <form>
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
                            <input class='input-calendar' type='date' name='date' value='$dateValue'>
                            <input class='calendar-submit' type='submit' value='Check Date'>
                        </form>
                    ";
                ?>
            </div>
            <table class='calendar'>

                <?php

                // Queries database for bookings for hour interval 08-23
                $bookings = BookingRepository::getBookingForDate($date);


                // Iterates over results to get header TutorId's
                $TAsHeader = [];
                foreach ($bookings as $booking) {
                    $TAsHeader[$booking->getTutorId()] = $booking->getTutorId();
                }
                foreach (array_keys($TAsHeader) as $key) {
                    $TAname = UserRepository::read($TAsHeader[$key])->getFirstName();
                    echo "<th>$TAname</th>";
                }


                // Iterates over bookings and puts arrays containing timeslots at array index using TutorId
                $timeSlots = [];
                foreach ($bookings as $booking) {
                    $timeSlots[$booking->getBookingTime()->format('H:i')][$booking->getTutorId()] = $booking->getStudentId();
                }

                // Creates table with timeslots
                foreach (array_keys($timeSlots) as $timeSlot) {
                    echo "<tr>";
                    foreach ($timeSlots[$timeSlot] as $bookedStatus) {
                        // Checks if the timeSlot has a studentId (meaning it's booked)
                        if ($bookedStatus) {
                            // Is booked
                            echo "<td class='unavailable-timeslot'>$timeSlot</td>";
                        } else {
                            // Is available
                            echo "<td class='available-timeSlot'><input class='book-checkbox' type='checkbox'>$timeSlot</td>";
                            // TODO Modify calendar view to show 7 columns (instead of TAs), with 7 days from today
                            // TODO selected upon submission iterate over and create booking using:
                            // TODO TAid, bookingTime and other fields required
                        }

                    }
                    echo "</tr>";
                }


                ?>

            </table>



        </div>
        <input class="submit-button" type="submit" name="book" value="Book Timeslots">
    </form>
</div>