<?php


namespace Views\Bookings\BookingsController;

require("../../../autoloader.php");

use Exception;
use Application\Validators\Auth;
use Infrastructure\Repositories\BookingRepository;

// Starts session, and checks if user is logged in. If not, redirects to login page
if (!Auth::checkAuth()) {
    header('Location: ../User/Login.php');
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


// Opens message conversation with the Tutor using studentId and tutorId
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'messageUser') {
    // Relocates to "message" page with tutor
    $userId = $_POST['userId'];
    echo "$userId";
}

// TODO feature: add to calendar using icalendar subscription (possibly redundant due to reminder email being sent
// Opens message conversation with the Tutor using studentId and tutorId
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addToCalendar') {
    // Creates calendar event with title, datetime start and datetime end after
    echo "Subscribed to booking successfully";
}