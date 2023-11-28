<?php

namespace Views\Book\CreateBookingController;

require("../../../autoloader.php");

use Application\Validators\Auth;
use Application\Constants\SessionConst;
use Infrastructure\Repositories\BookingRepository;
use Core\Entities\Booking;
use DateTime;

if (!Auth::checkAuth() || $_SESSION[SessionConst::USER_TYPE] !== 'Tutor') {
    header('Location: ../User/Login.php');
    exit();
}

// Removes the booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'removeBooking') {
    BookingRepository::delete($_POST['bookingId']);
}


// Creates a booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'createBooking') {
    $booking = new Booking();
    $booking->setTutorId($_SESSION[SessionConst::USER_ID]);
    $booking->setBookingTime(new DateTime($_POST['bookingTime']));
    $booking->setStatus($_POST['bookingLocation']);

    BookingRepository::create($booking);
}