<?php


namespace Core\Entities;

use DateTime;

class Booking
{
    private ?int $bookingId;
    private ?int $studentId;
    private int $tutorId;
    private DateTime $bookingTime;
    private string $location;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function getBookingId(): int
    {
        return $this->bookingId;
    }

    public function setBookingId(int $bookingId): self
    {
        $this->bookingId = $bookingId;
        return $this;
    }

    public function getStudentId(): ?int
    {
        return $this->studentId;
    }

    public function setStudentId(?int $studentId): self
    {
        $this->studentId = $studentId;
        return $this;
    }

    public function getTutorId(): int
    {
        return $this->tutorId;
    }

    public function setTutorId(int $tutorId): self
    {
        $this->tutorId = $tutorId;
        return $this;
    }

    public function getBookingTime(): DateTime
    {
        return $this->bookingTime;
    }

    public function setBookingTime(DateTime $bookingTime): self
    {
        $this->bookingTime = $bookingTime;
        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }



}