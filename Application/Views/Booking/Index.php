<?php
    require ("../../../autoloader.php");
    use Core\Entities\Booking;
use Infrastructure\Repositories\BookingRepository;


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

    <form  class="form-group" id="form" action="" method="POST">
        <div class="booking-view">
            <div class="booking-date">30/10/2023</div>

            <table class="calendar">
                <?php
                // Makes the column names
                echo "<tr>";
                echo "<th>DATETIME</th>"; // Adds empty first column value for DATETIME
                $dates = array("30-10-2023");
                foreach ($dates as $date) {
                    echo "<th>$date</th>";
                }
                echo "</tr>";

                for ($time = 480; $time < 1440; $time += 15) {
                    $hour = floor(($time / 60));
                    $minute = $time % 60;
                    $dateTime = strval($hour) . ":" . strval($minute);
                    echo "<tr>";
                    echo "<td class='booking-time'>$dateTime</td>"; // Adds empty first column value for DATETIME
                    foreach ($dates as $date) {
                        $paddingPreviousTimeSlot = 'unbooked-timeslot';
                        echo "<td class='$paddingPreviousTimeSlot'><input name='$dateTime-$dateTime' class='book-checkbox' type='checkbox' value='{$dateTime}'>$dateTime</td>";

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
        <input class="submit-button" type="submit" name="createBookings" value="Make Timeslots Available">
    </form>


</div>