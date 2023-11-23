<?php

namespace Interfaces;

use Core\Entities\Booking;

interface IBookingRepository
{
    public static function create(Booking $user);
    public static function read($id);
    public static function update($booking);
    public static function delete($id);
}