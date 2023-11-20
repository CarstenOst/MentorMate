<?php

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

                for ($time = 0; $time < 1440; $time += 15) {
                    $hour = floor(($time / 60));
                    $minute = $time % 60;
                    $dateTime = strval($hour) . ":" . strval($minute);
                    echo "<tr>";
                    echo "<td>$dateTime</td>"; // Adds empty first column value for DATETIME
                    foreach ($TAs as $TA) {
                        $paddingPreviousTimeSlot = $time % 60 == 0 ? 'available-timeSlot' : '';
                        echo "<td class='$paddingPreviousTimeSlot'><input type='checkbox' name='$TA-$dateTime'>$dateTime</td>";
                    }
                    echo "</tr>";
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
