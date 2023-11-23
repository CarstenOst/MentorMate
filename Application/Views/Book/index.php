<?php
require("../../../autoloader.php");

use Infrastructure\Database\DBConnector;
use Infrastructure\Repositories\BookingRepository;

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
            <div class="booking-date">30/10/2023</div>

            <table class="calendar">
                <?php
                // Makes the column names
                echo "<tr>";
                echo "<th>DATETIME</th>"; // Adds empty first column value for DATETIME
                $TAs = array("Henry", "John", "Alice", "Beatrice",);
                foreach ($TAs as $TA) {
                    echo "<th>$TA</th>";
                }
                echo "</tr>";

                for ($time = 480; $time < 1440; $time += 15) {
                    $hour = floor(($time / 60));
                    $minute = $time % 60;
                    $dateTime = strval($hour) . ":" . strval($minute);
                    echo "<tr>";
                    echo "<td class='booking-time'>$dateTime</td>"; // Adds empty first column value for DATETIME
                    foreach ($TAs as $TA) {
                        if ($time % 60 != 0) {
                            echo "<td class='unavailable-timeslot'></td>";
                        }
                        else {
                            $paddingPreviousTimeSlot = $time % 60 == 0 ? 'available-timeSlot' : '';
                            echo "<td class='$paddingPreviousTimeSlot'><input  class='book-checkbox'  type='checkbox' name='$TA-$dateTime'>$dateTime</td>";
                        }

                    }
                    echo "</tr>";
                }
                ?>
            </table>

            <table class='tabell'>
                <tr>
                    <th>bookingId</th>
                    <th>studentId</th>
                    <th>tutorId</th>
                    <th>bookingTime</th>
                    <th>status</th>
                    <th>createdAt</th>
                    <th>updatedAt</th>
                </tr>
                <?php

                // Gets today's date
                $dato = new DateTime();
                // TODO add button for "date" choice (with today as standard, and future dates as options)

                echo "<br><br>Date for fetching times: {$dato->format('Y-m-d')}
                <br>If none are shown, there aren't any for that date between 08:00:00 and 23:59:59.<br><br>";

                // Queries database for bookings for hour interval 08-23
                $bookinger = BookingRepository::getBookingForDate($dato);

                // Iterates over results and presents them in a simple table for development TODO replace this
                foreach ($bookinger as $booking) {
                    echo "
                    <tr>
                        <td>{$booking->getBookingId()}</td>
                        <td>{$booking->getStudentId()}</td>
                        <td>{$booking->getTutorId()}</td>
                        <td>{$booking->getBookingTime()->format('Y-m-d H:i:s')}</td>
                        <td>{$booking->getStatus()}</td>
                        <td>{$booking->getCreatedAt()->format('Y-m-d H:i:s')}</td>
                        <td>{$booking->getUpdatedAt()->format('Y-m-d H:i:s')}</td>
                    </tr>
                ";
                }


                ?>
            </table>

            <!--
                for TA in TAs
                    for timeSlot in TACurrentDateAvailableTimeSlots
                        if timeSlot in TAAvailableSlots
                            green checkbox input field with time start and time end
                            (if the previous timeSlot exists, and ended when the current starts: don't add margin bottom)
                            (if the previous timeSlot exists, and didn't end when the current starts: add margin bottom equal to one green checkbox input field)

            -->

            <!-- TA bookings iterated, maybe as a form where the available hours are checkboxes.
                Each checkbox is linked to the TA where; each TA is looped,
                then each of their available/non-available time slots are looped. Resulting in a grid of
            -->
        </div>
        <input class="submit-button" type="submit" name="book" value="Book Timeslots">
    </form>
</div>