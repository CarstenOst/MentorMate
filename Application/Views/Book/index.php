<html>
<head>
    <link rel="stylesheet" href="/Assets/style.css">
</head>

<body>
<div class="side-menu">

</div>

<div class="main-view">

    <div class="booking-view">
        <div>Current Date</div>
        <div class="calendar">

            <!-- column 1 with TA 1 bookings iterated -->
            <div class="calendar-column">
                <div class="time-slots-columns">
                    <?php
                    $TAs = array(
                        "Henry" => array("09:00", "09:15", "09:30"),
                        "John"  => array("08:00", "09:30", "12:30"),
                        "Alice"  => array("16:00", "17:15", "17:30"),
                        "Beatrice" => array("10:00", "12:00", "12:15"),
                    );
                    foreach (array_keys($TAs) as $TA) {
                        $scheduleTA = "<div class='TA-column'><div>$TA</div>";
                        foreach ($TAs[$TA] as $timeSlot) {
                            $paddingPreviousTimeSlot = true ? 'time-slot-margin' : '';
                            $scheduleTA .= "
                                            <div class='available-timeSlot {$paddingPreviousTimeSlot}'>
                                                <input type='checkbox' name='{$TA}-{$timeSlot}'>
                                                $timeSlot
                                            </div>
                                        ";
                        }
                        $scheduleTA .= "</div>";
                        echo $scheduleTA;
                    }
                    ?>
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
            </div>
        </div>
    </div>
</div>

<?php

?>
</body>

</html>