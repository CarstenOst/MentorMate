<?php

namespace Core\Entities;
class Booking
{
    private int $bookingId;
    private int $studentId;
    private int $tutorId;
    private int $bookingTime;
    private int $status;
    private int $createdAt;
    private int $updatedAt;

    // Getter methods

    public function getBookingId(): int
    {
        return $this->bookingId;
    }

    public function getStudentId(): int
    {
        return $this->studentId;
    }

    public function getTutorId(): int
    {
        return $this->tutorId;
    }

    public function getBookingTime(): int
    {
        return $this->bookingTime;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    // Setter methods

    public function setBookingId(int $bookingId): void
    {
        $this->bookingId = $bookingId;
    }

    public function setStudentId(int $studentId): void
    {
        $this->studentId = $studentId;
    }

    public function setTutorId(int $tutorId): void
    {
        $this->tutorId = $tutorId;
    }

    public function setBookingTime(int $bookingTime): void
    {
        $this->bookingTime = $bookingTime;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}