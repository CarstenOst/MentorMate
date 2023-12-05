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

?>



<html lang="en">
<head>
    <link rel="stylesheet" href="../../Assets/style.css">
    <script src="https://kit.fontawesome.com/5928831ae4.js" crossorigin="anonymous"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        // Waits for page to load, then makes functions available globally
        document.addEventListener("DOMContentLoaded", function () {
            window.confirmCancellation = function confirmCancelation(bookingId) {
                // Confirmation dialog before cancelling
                var result = confirm("Are you sure you want cancel this booking?");

                // Use AJAX to call a PHP controller action
                if (result) {
                    $.ajax({
                        type: "POST",
                        url: "../../Controllers/BookingController.php",
                        data: {
                            action: "cancelBooking",
                            bookingId: bookingId
                        },
                        error: function (data) {
                            let response = JSON.parse(data);
                            alert(response.error);
                        }
                    });
                }
            }


            window.messageUser = function messageUser(userId) {
                // Use AJAX to call a PHP controller action
                $.ajax({
                    type: "POST",
                    url: "../../Controllers/BookingController.php",
                    data: {
                        action: "messageUser",
                        userId: userId
                    },
                    success: function (data) {
                        let response = JSON.parse(data);
                        window.location.href = response.redirect;
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        // Directly access the error message without parsing JSON
                        alert("Error: " + jqXHR.responseText);
                    }
                });
            }

            window.viewUser = function viewUser(userId) {
                // Use AJAX to call a PHP controller action
                $.ajax({
                    type: "POST",
                    url: "../../Controllers/BookingController.php",
                    data: {
                        action: "viewUser",
                        userId: userId,
                    },
                    success: function (data) {
                        const response = JSON.parse(data);
                        window.location.href = response.redirect;
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

        <div>
            <h2>Your bookings</h2>
        </div>


        <form method='POST' action=''>
            <table class='calendar'>
                <?php
                // Sets variable to view the user their content
                $isTutor = $_SESSION[SessionConst::USER_TYPE] === 'Tutor';
                // Gets tutor's or student's upcoming bookings and creates headers for table
                $date = new DateTime();

                if ($isTutor) {
                    list($bookings, $participant) = BookingRepository::getTutorBookings($date, $_SESSION[SessionConst::USER_ID]);
                    $bookingHeaders = ["Booking date", "Location", "Student", "Cancel Timeslot", "Message", "Add to calendar"];
                } else {
                    list($bookings, $participant) = BookingRepository::getStudentBookings($date, $_SESSION[SessionConst::USER_ID]);
                    $bookingHeaders = ["Booking date", "Location", "Tutor", "Cancel Timeslot", "Message", "Add to calendar"];
                }



                // Displays standard view if no upcoming bookings found, otherwise populates table with upcoming bookings
                if (sizeof($bookings) == 0) {
                    echo "
                        <tr>
                            <th>Seems like you have no future bookings...</th>
                        </tr>
                    ";
                } else {
                    // Table headers
                    echo "<tr>";
                    foreach ($bookingHeaders as $header) {
                        echo "<th>$header</th>";
                    }
                    echo "</tr>";

                    // Populates table with booking rows
                    $bookingsCount = count($bookings);
                    for ($i = 0; $i < $bookingsCount; $i++) {
                        $timeSlotEnd = $bookings[$i]->getBookingTime()->modify('+15 minutes')->format('H:i');
                        // Gets info about associated user (if there is one associated with the booking)
                        $userId = $isTutor ? $bookings[$i]->getStudentId() : $bookings[$i]->getTutorId();
                        $userName = $participant[$i];
                        $bookingId = $bookings[$i]->getBookingId();

                        if ($bookings[$i]->getStudentId()) {
                            echo "
                                <tr>
                                    <td>
                                        <i class='calendar-icon fa-regular fa-calendar'></i> {$bookings[$i]->getBookingTime()->format('d-m-Y')}
                                        <br>
                                        <i class='clock-icon fa-regular fa-clock'></i> {$bookings[$i]->getBookingTime()->format('H:i')}-$timeSlotEnd
                                    </td>
                                    <td>
                                        <i class='location-icon fa-regular fa-location-dot'></i> {$bookings[$i]->getLocation()}
                                    </td>
                                    <td>
                                        <button class='table-button' onclick='viewUser($userId)'><i class='fa-solid fa-user'></i> $userName</button>
                                    </td>
                                    <td>
                                        <button class='table-button' onclick='confirmCancelation($bookingId)'><i class='cancel-icon fa-solid fa-ban'></i> Cancel</button>
                                    </td>
                                    <td>
                                        <button class='table-button' onclick='messageUser($userId)'><i class='message-icon fa-solid fa-message'></i> Message</button>
                                    </td>
                                    <td>
                                        <button class='table-button'><i class='calendar-icon fa-regular fa-calendar-plus'></i> Add boooking</button>
                                    </td>
                                </tr>
                            ";
                        } else {
                            echo "
                                <tr>
                                    <td>
                                        <i class='calendar-icon fa-regular fa-calendar'></i> {$bookings[$i]->getBookingTime()->format('d-m-Y')}
                                        <br>
                                        <i class='clock-icon fa-regular fa-clock'></i> {$bookings[$i]->getBookingTime()->format('H:i')}-$timeSlotEnd
                                    </td>
                                    <td>
                                        <i class='location-icon fa-regular fa-location-dot'></i> {$bookings[$i]->getLocation()}
                                    </td>
                                    <td>
                                        <i class='fa-solid fa-user'></i>
                                    </td>
                                    <td>
                                        <button class='table-button' onclick='confirmCancelation($bookingId)'><i class='cancel-icon fa-solid fa-ban'></i> Cancel</button>
                                    </td>
                                    <td>
                                        
                                    </td>
                                    <td>
                                        <button class='table-button'><i class='calendar-icon fa-regular fa-calendar-plus'></i> Add boooking</button>
                                    </td>
                                </tr>
                            ";
                        }
                    }

                }

                ?>

            </table>
        </form>
    </div>



</body>
