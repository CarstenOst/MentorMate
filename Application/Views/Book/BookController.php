<?php

namespace Views\Book\BookController;

require("../../../autoloader.php");

use Infrastructure\Repositories\BookingRepository;


// Cancels the booking by updating the studentId to be null
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancelBooking') {
    $booking = BookingRepository::read($_POST['bookingId']);
    $booking->setStudentId(null);
    BookingRepository::update($booking);
}


// Books the booking by updating the studentId to the studentId
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bookBooking') {
    $booking = BookingRepository::read($_POST['bookingId']);
    $studentId = $_POST['studentId'];
    $booking->setStudentId($studentId);
    BookingRepository::update($booking);
}