<?php


namespace Views\Bookings\BookingController;

require("../../../autoloader.php");

use Infrastructure\Repositories\BookingRepository;


// Cancels the booking by updating the studentId to be null
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancelBooking') {
    $booking = BookingRepository::read($_POST['bookingId']);
    $booking->setStudentId(null);
    BookingRepository::update($booking);
}


// Opens message conversation with the Tutor using studentId and tutorId
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'messageTutor') {
    // Relocates to "message" page with tutor
    $tutorId = $_POST['tutorId'];
    echo "$tutorId";
}

// TODO feature: add to calendar using icalendar subscription (possibly redundant due to reminder email being sent
// Opens message conversation with the Tutor using studentId and tutorId
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addToCalendar') {
    // Creates calendar event with title, datetime start and datetime end after
    echo "Subscribed to booking successfully";
}