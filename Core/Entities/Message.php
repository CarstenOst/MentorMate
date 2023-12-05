<?php

namespace Core\Entities;

use DateTime;

class Message
{
    private ?int $messageId;
    private int $senderId;
    private int $receiverId;
    private ?DateTime $sentAt;
    private string $messageText;
    private ?bool $isRead;

    public function __construct(?int $messageId, int $senderId, int $receiverId, ?DateTime $sentAt, string $messageText, ?bool $isRead)
    {
        $this->messageId = $messageId;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->sentAt = $sentAt;
        $this->messageText = $messageText;
        $this->isRead = $isRead;
    }

    // Getter and setter for messageId
    public function getMessageId(): int
    {
        return $this->messageId;
    }

    public function setMessageId(int $messageId): void
    {
        $this->messageId = $messageId;
    }

    // Getter and setter for senderId
    public function getSenderId(): int
    {
        return $this->senderId;
    }

    public function setSenderId(int $senderId): void
    {
        $this->senderId = $senderId;
    }

    // Getter and setter for receiverId
    public function getReceiverId(): int
    {
        return $this->receiverId;
    }

    public function setReceiverId(int $receiverId): void
    {
        $this->receiverId = $receiverId;
    }

    // Getter and setter for sentAt
    public function getSentAt(): DateTime
    {
        return $this->sentAt;
    }

    public function setSentAt(DateTime $sentAt): void
    {
        $this->sentAt = $sentAt;
    }

    // Getter and setter for messageText
    public function getMessageText(): string
    {
        return $this->messageText;
    }

    public function setMessageText(string $messageText): void
    {
        $this->messageText = $messageText;
    }

    // Getter and setter for isRead
    public function getIsRead(): bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): void
    {
        $this->isRead = $isRead;
    }

}
