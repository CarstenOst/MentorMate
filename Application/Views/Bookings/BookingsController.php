<?php


namespace Views\Bookings\BookingController;

require("../../../autoloader.php");

use Infrastructure\Repositories\BookingRepository;


// TODO modify this section into a check for row 'buttons' that were clicked to; cancel, message or ?view TA profile? and ?add to calendar?
// Cancels the booking by updating the studentId to be null
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancelBooking') {
    $booking = BookingRepository::read($_POST['bookingId']);
    $booking->setStudentId(null);
   BookingRepository::update($booking);
}


// Opens message conversation with the Tutor using studentId and tutorId
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'messageTutor') {
    $tutorId = $_POST['tutorId'];
    echo "$tutorId";
}
