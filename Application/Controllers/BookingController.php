<?php

namespace Application\Controllers\BookingController;

require("../../autoloader.php");

use Application\Constants\Secrets;
use Application\Functions\SendMail;
use DateTime;
use Exception;
use Core\Entities\Booking;
use Application\Validators\Auth;
use Application\Constants\SessionConst;
use Infrastructure\Repositories\BookingRepository;

// Starts session, and checks if user is logged in. If not, redirects to login page
if (!Auth::checkAuth()) {
    header('Location: ../User/Login.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        // Actions for calendar interaction
        case 'previousDate':
            $previousDate = $_POST['previousDate'];
            echo json_encode(['redirect' => "./index.php?date=$previousDate"]);
            break;

        case 'nextDate':
            $nextDate = $_POST['nextDate'];
            echo json_encode(['redirect' => "./index.php?date=$nextDate"]);
            break;

        case 'previousDateWithLocation':
            $previousDate = $_POST['previousDate'];
            $location = $_POST['location'];
            echo json_encode(['redirect' => "./index.php?date=$previousDate&location=$location"]);
            break;

        case 'nextDateWithLocation':
            $nextDate = $_POST['nextDate'];
            $location = $_POST['location'];
            echo json_encode(['redirect' => "./index.php?date=$nextDate&location=$location"]);
            break;


        // Tutor actions for interacting with bookings
        case 'createBooking':
            try {
                $booking = new Booking();
                $booking->setTutorId($_SESSION[SessionConst::USER_ID]);
                $booking->setBookingTime(new DateTime($_POST['bookingTime']));
                $booking->setLocation($_POST['bookingLocation']);
                $bookingId = BookingRepository::create($booking);
                echo json_encode(['message' => "Successfully created the booking.", "bookingId" => $bookingId]);
            } catch (Exception $error) {
                http_response_code(400);
                echo json_encode(['error' => "Failed to create the booking."]);
            }
            break;

        case 'removeBooking':
            try {
                $booking = BookingRepository::read($_POST['bookingId']);
                if ($booking->getStudentId()) {
                    SendMail::sendMailTo(
                    // Replace this with the user email (chose not to have this dynamically set to be the user email)
                        Secrets::HARDCODED_MAIL_FOR_TESTING_ONLY,
                        "Your timeslot at {$booking->getBookingTime()->format('d-m-y H:i')} at {$booking->getLocation()} was cancelled",
                        'The booked timeslot was deleted by your tutor.',
                        'Message (alt - plain text)'
                    );
                }
                BookingRepository::delete($_POST['bookingId']);
                echo json_encode(['message' => "Successfully removed the booking."]);
            } catch (Exception $error) {
                http_response_code(400);
                echo json_encode(['error' => "Failed to remove the booking."]);
            }
            break;


        // Student actions for interacting with bookings
        case 'bookBooking':
            try {
                $booking = BookingRepository::read($_POST['bookingId']);
                $studentId = $_SESSION[SessionConst::USER_ID];
                $booking->setStudentId($studentId);
                BookingRepository::update($booking);
                SendMail::sendMailTo(
                    // Replace this with the user email (chose not to have this dynamically set to be the user email)
                    Secrets::HARDCODED_MAIL_FOR_TESTING_ONLY,
                    "New booked timeslot at {$booking->getBookingTime()->format('d-m-y H:i')}",
                    "Time: {$booking->getBookingTime()->format('d-m-y H:i')} <br>Location: {$booking->getLocation()}.",
                    "Message (alt - plain text)"
                );
                echo json_encode(['message' => "Successfully booked the booking."]);
            } catch (Exception $error) {
                http_response_code(400);
                echo json_encode(['error' => "Failed to book the booking."]);
            }
            break;

        case 'cancelBooking':
            try {
                // TODO Add safety check to see if the student is the one who booked the booking
                $booking = BookingRepository::read($_POST['bookingId']);
                if ($booking->getStudentId()) {
                    SendMail::sendMailTo(
                    // Replace this with the user email (chose not to have this dynamically set to be the user email)
                        Secrets::HARDCODED_MAIL_FOR_TESTING_ONLY,
                        "Your timeslot at {$booking->getBookingTime()->format('d-m-y H:i')} at {$booking->getLocation()} was cancelled",
                        'The booked timeslot was cancelled by your tutor.',
                        'Message (alt - plain text)'
                    );
                }
                $booking->setStudentId(null);
                BookingRepository::update($booking);
                echo json_encode(['message' => "Successfully cancelled the booking."]);
            } catch (Exception $error) {
                http_response_code(400);
                echo json_encode(['error' => "Failed to cancel the booking."]);
            }
            break;


        // Actions for viewing and interacting with users
        case 'viewUser':
            $_SESSION['last_viewed_profile'] = $_POST['userId'];
            echo json_encode(['redirect' => "/MentorMate/Application/Views/User/OthersProfile.php"]);
            break;

        case 'messageUser':
            // Checks if the user exists before redirecting
            if ($_POST['userId']) {
                $_SESSION['chat_last_receiver'] = $_POST['userId'];
                echo json_encode(["redirect" => "/MentorMate/Application/Views/Messages/index.php"]);
            } else {
                http_response_code(400);
                echo json_encode(["errorThrown" => "Failed to message user."]);
            }
            break;
    }

    exit();
}
