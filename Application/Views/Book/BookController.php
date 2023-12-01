<?php

namespace Views\Book\BookController;

require("../../../autoloader.php");

use Exception;
use Application\Constants\SessionConst;
use Application\Validators\Auth;
use Infrastructure\Repositories\BookingRepository;

// Starts session, and checks if user is logged in. If not, redirects to login page
if (!Auth::checkAuth()) {
    header('Location: ../User/Login.php');
    exit();
}

if ($_SESSION[SessionConst::USER_TYPE] !== 'Student') {
    header('Location: ../User/Profile.php');
}


// Sets the date to be the previous day
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'previousDate') {
    $previousDate = $_POST['previousDate'];
    echo json_encode(['redirect' => "./index.php?date=$previousDate"]);
    exit();
}

// Sets the date to be the next day
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'nextDate') {
    $nextDate = $_POST['nextDate'];
    echo json_encode(['redirect' => "./index.php?date=$nextDate"]);
    exit();
}


// Cancels the booking by updating the studentId to be null
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancelBooking') {
    try {
        $booking = BookingRepository::read($_POST['bookingId']);
        $booking->setStudentId(null);
        BookingRepository::update($booking);

        // Returns status of the action
        echo json_encode(['message' => "Successfully cancelled the booking."]);

    } catch (Exception $error) {
        // Returns status of the action
        http_response_code(400);
        echo json_encode(['error' => "Failed to cancel the booking."]);
    }
}


// Books the booking by updating the studentId to the studentId
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bookBooking') {
    try {
        $booking = BookingRepository::read($_POST['bookingId']);
        $studentId = $_SESSION[SessionConst::USER_ID];
        $booking->setStudentId($studentId);
        BookingRepository::update($booking);

        // Returns status of the action
        echo json_encode(['message' => "Successfully booked the booking."]);

    } catch (Exception $error) {
        // Returns status of the action
        http_response_code(400);
        echo json_encode(['error' => "Failed to book the booking."]);
    }

    exit();
}