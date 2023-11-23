<?php
namespace Booking;
use Core\Entities\Booking;
use Core\Entities\User;
use Infrastructure\Repositories\BookingRepository;

class CreateBooking
{
    public static function submitBookings(array $formData): bool {

        $booking = new Booking();
        $booking->setStudentId($formData['studentId']);
        $booking->setTutorId($formData['tutorId']); // TODO Should be swapped to the tutorID, currently locked to a fixed user
        $booking->setBookingTime($formData['bookingTime']);
        $booking->setStatus($formData['status']);


        // Returns the status of the sql updating the user
        return BookingRepository::create($booking);

    }
}
