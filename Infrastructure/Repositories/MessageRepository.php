<?php

namespace Repositories;
include '../../autoloader.php';

use Infrastructure\Database\DBConnector;
use Core\Entities\Message;
use PDOException;
use PDOStatement;
use Exception;
use DateTime;
use PDO;
class MessageRepository
{

    /**
     * Could also be called sendMessage.
     * Could also refactor to not use the Message entity, but rather just the parameters.
     *
     * @param Message $message The Message entity needs to have senderId, receiverId and messageText set.
     * @return int|bool Returns the id of the created message or false if the message could not be created
     * @throws Exception
     */
    public static function create(Message $message): int|bool
    {
        $query = "INSERT INTO Message (
                  senderId, 
                  receiverId, 
                  messageText) 
                VALUES (:senderId, 
                        :receiverId, 
                        :messageText);
                        ";

        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);

        $sql->bindValue(':senderId', $message->getSenderId(), PDO::PARAM_INT);
        $sql->bindValue(':receiverId', $message->getReceiverId(), PDO::PARAM_INT);
        $sql->bindValue(':messageText', $message->getMessageText());

        try {
            // Execute the statement
            $sql->execute();
            echo "Message created successfully";
            echo "<br>";
            return $connection->lastInsertId();

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }


    /**
     * This method is used to get all unread messages sent to a user
     *
     * Since we're not using a websocket, we need to poll the database for new messages.
     * This is the one to poll from.
     *
     * @param int $receiverId insert the id of the user to get messages not read by this user
     * @return array|bool Returns an array of messages or false if no messages were found
     */
    public static function getUnreadMessagesByReceiverId(int $receiverId): array|bool
    {
        // We probably do not need to join the user with the message, but it's here for now.
        // We definitely do not need to join the logged-in user with the message for this query.
        // So we will not.
        $query = "SELECT * FROM Message 
                JOIN 
                    User on Message.senderId = User.userId
                WHERE 
                    receiverId = :receiverId
                AND 
                    isRead = 0";

        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);
        $sql->bindValue(':receiverId', $receiverId, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllMessagesBetweenUsers(int $senderId, int $receiverId): array|bool
    {
        $query = "SELECT * FROM Message 
                JOIN 
                    User on Message.senderId = User.userId
                WHERE 
                    (receiverId = :receiverId AND senderId = :senderId)
                OR
                    (receiverId = :senderId AND senderId = :receiverId)";

        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);
        $sql->bindValue(':receiverId', $receiverId, PDO::PARAM_INT);
        $sql->bindValue(':senderId', $senderId, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

}

$messages = MessageRepository::getAllMessagesBetweenUsers(1, 2);

foreach ($messages as $message) {
    echo $message['messageText'] . " sent at " . $message['sentAt'] . " from " . $message['firstName'] . " " .
        $message['lastName'];
    echo "<br>";
    echo "<br>";
}

