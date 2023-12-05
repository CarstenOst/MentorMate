<?php

namespace Application\Controllers\BookingController;

require("../../autoloader.php");

use Core\Entities\Booking;
use DateTime;
use Exception;
use Application\Constants\SessionConst;
use Application\Validators\Auth;
use Infrastructure\Repositories\BookingRepository;
use Infrastructure\Repositories\UserRepository;

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
                BookingRepository::create($booking);
                echo json_encode(['message' => "Successfully created the booking."]);
            } catch (Exception $error) {
                http_response_code(400);
                echo json_encode(['error' => "Failed to create the booking."]);
            }
            break;

        case 'removeBooking':
            try {
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
                echo json_encode(['message' => "Successfully booked the booking."]);
            } catch (Exception $error) {
                http_response_code(400);
                echo json_encode(['error' => "Failed to book the booking."]);
            }
            break;

        case 'cancelBooking':
            try {
                $booking = BookingRepository::read($_POST['bookingId']);
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


        // Actions for user functions
        case 'addToCalendar':
            // Logic for adding to calendar using icalendar subscription
            echo json_encode(["message" => "Added to calendar"]);
            break;
    }

    exit();
}