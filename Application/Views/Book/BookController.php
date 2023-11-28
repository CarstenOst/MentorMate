<?php

namespace Views\Book\BookController;

require("../../../autoloader.php");

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