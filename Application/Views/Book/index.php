<?php
require("../../../autoloader.php");

use Infrastructure\Database\DBConnector;
use Infrastructure\Repositories\BookingRepository;
use Infrastructure\Repositories\UserRepository;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $formData = $_POST;
        echo "success!";
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



                echo "<br><br>Date for fetching times: {$date->format('Y-m-d')}
                <br>If none are shown, there aren't any for that date between 08:00:00 and 23:59:59.<br><br>";

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
                            echo "<td class='available-timeSlot'><input  class='book-checkbox'  type='checkbox'>$timeSlot</td>";
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
