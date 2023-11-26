<?php
require("../../../autoloader.php");

use Application\Constants\SessionConst;
use Application\Validators\Auth;
use Infrastructure\Repositories\BookingRepository;
use Infrastructure\Repositories\UserRepository;
use Core\Entities\Booking;


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


// TODO modify this section into a check for row 'buttons' that were clicked to; cancel, message or ?view TA profile? and ?add to calendar?
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancelBooking') {
    BookingRepository::update();

    // Provide a response (optional)
    echo 'Action performed successfully!';
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
    <script>
        function confirmAction() {
            // Display a confirm dialog
            var result = confirm("Are you sure you want to perform this action?");

            // If the user clicks 'OK', proceed with the action
            if (result) {
                // Use AJAX to call a PHP script
                $.ajax({
                    type: "POST",
                    url: "index.php",
                    data: { action: "cancelBooking" },
                    success: function(response) {
                        alert(response); // Show a response message
                    }
                });
            } else {
                // If the user clicks 'Cancel', do nothing or provide feedback
                alert("Action canceled.");
            }
        }
    </script>
</head>

<body>
<div class="side-menu">
    <ul>
        <li><a class="logo-title" href="#">
                MentorMate
            </a>
        </li>
        <li>
            <a href="../../Views/User/Profile.php" class="side-menu-profile-link">
                <div class="profile">
                    <i class="profile-icon fa-solid fa-user"></i>
                    <p>Profile</p>
                </div>
            </a>
        </li>
        <li><a href="index.php">Book</a></li>
        <li><a href="../Bookings/index.php">Bookings</a></li>
        <li><a href="#">Messages</a></li>
        <li><a href="index.php?logout=1">Log Out</a></li>
    </ul>
</div>

<div class="main-view">

    <div class="booking-view">
        <div>
            <h2>Your bookings</h2>
        </div>


        <form method='POST' action=''>
            <table class='calendar'>
                <?php

                // Queries database for bookings for hour interval 08-23
                $date = new DateTime();
                $bookings = BookingRepository::getStudentBookings($date, $_SESSION[SessionConst::USER_ID]);
                if (sizeof($bookings) == 0) {
                    echo "<br>Seems like you have no future bookings...";
                } else {
                    // Creates headers for table


                    $boookingHeaders = ["Booking date", "Location", "Tutor", "Cancel", "Message", "Add to calendar"];
                    echo "<tr>";
                    foreach ($boookingHeaders as $header) {
                        echo "<th>$header</th>";
                    }
                    echo "</tr>";

                    // Populates table with booking rows
                    foreach ($bookings as $booking) {
                        $tutorName = UserRepository::read($booking->getTutorId())->getFirstName();
                        echo "
                            <tr>
                                <td>{$booking->getBookingTime()->format('Y-m-d H:i:s')}</td>
                                <td>{$booking->getStatus()}</td>
                                <td><input type='button' class='table-button'><i class='fa-solid fa-user'></i> {$tutorName}</input></td>
                                <td>
                                    <input type='button' class='table-button'><i class='cancel-icon fa-solid fa-ban'></i></input>
                                </td>
                                <td>
                                    <input type='button' class='table-button'><i class='message-icon fa-solid fa-message'></i></input>
                                </td>
                                <td>
                                    <input type='button' class='table-button'><i class='add-to-calendar-icon fa-regular fa-calendar'></i> Add boooking</input>
                                </td>
                            </tr>
                        ";
                    }

                }

                ?>

            </table>
        </form>
    </div>


</div>