<?php

namespace Views\Book\CreateBookingController;

require("../../../autoloader.php");

use Exception;
use Application\Validators\Auth;
use Application\Constants\SessionConst;
use Infrastructure\Repositories\BookingRepository;
use Core\Entities\Booking;
use DateTime;

// Starts session, and checks if user is logged in. If not, redirects to login page
if (!Auth::checkAuth()) {
    header('Location: ../User/Login.php');
    exit();
}

if ($_SESSION[SessionConst::USER_TYPE] !== 'Tutor') {
    header('Location: ../User/Profile.php');
}

// Removes the booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'removeBooking') {
    try {
        BookingRepository::delete($_POST['bookingId']);

        // Returns status of the action
        echo json_encode(['message' => "Successfully removed the booking."]);

    } catch (Exception $error) {
        // Returns status of the action
        http_response_code(400);
        echo json_encode(['error' => "Failed to remove the booking."]);
    }
}


// Creates a booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'createBooking') {
    try {
        $booking = new Booking();
        $booking->setTutorId($_SESSION[SessionConst::USER_ID]);
        $booking->setBookingTime(new DateTime($_POST['bookingTime']));
        $booking->setLocation($_POST['bookingLocation']);
        BookingRepository::create($booking);

        // Returns status of the action
        echo json_encode(['message' => "Successfully created the booking."]);

    } catch (Exception $error) {
        // Returns status of the action
        http_response_code(400);
        echo json_encode(['error' => "Failed to create the booking."]);
    }

}