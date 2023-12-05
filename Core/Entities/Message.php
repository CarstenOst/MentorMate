<?php

namespace Core\Entities;

use DateTime;


class Message
{
    private ?int $messageId;
    private int $senderId;
    private int $receiverId;
    private DateTime $sentAt;
    private string $messageText;
    private int $isRead;


    // Getter methods
    public function getMessageId(): ?int
    {
        return $this->messageId;
    }
    public function getSenderId(): int
    {
        return $this->senderId;
    }
    public function getReceiverId(): int
    {
        return $this->receiverId;
    }
    public function getSentAt(): DateTime
    {
        return $this->sentAt;
    }
    public function getMessageText(): string
    {
        return $this->messageText;
    }
    public function getIsRead(): int
    {
        return $this->isRead;
    }

    // Setter methods
    public function setMessageId(?int $messageId): void
    {
        $this->messageId = $messageId;
    }
    public function setSenderId(int $senderId): void
    {
        $this->senderId = $senderId;
    }
    public function setReceiverId(int $receiverId): void
    {
        $this->receiverId = $receiverId;
    }
    public function setSentAt(DateTime $sentAt): void
    {
        $this->sentAt = $sentAt;
    }
    public function setMessageText(string $messageText): void
    {
        $this->messageText = $messageText;
    }
    public function setIsRead(int $isRead): void
    {
        $this->isRead = $isRead;
    }
}