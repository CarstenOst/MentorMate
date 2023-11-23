<?php


namespace Core\Entities;

use DateTime;

class Booking
{
    private int $bookingId;
    private ?int $studentId;
    private int $tutorId;
    private DateTime $bookingTime;
    private string $status;
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
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