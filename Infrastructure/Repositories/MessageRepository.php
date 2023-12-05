<?php

namespace Infrastructure\Repositories;

use Core\Entities\Message;
use Infrastructure\Database\DBConnector;
use PDOException;
use PDOStatement;
use DateTime;
use PDO;

class MessageRepository
{

    public static function create(Message $message): int
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
        $sql->bindValue(':senderId', $message->getSenderId());
        $sql->bindValue(':receiverId', $message->getReceiverId());
        $sql->bindValue(':messageText', $message->getMessageText());

        try {
            // Execute the statement
            $sql->execute();
            return $sql->fetch(PDO::FETCH_ASSOC) ?? -1 ;

        } catch (PDOException $e) {
            // Handle other errors or rethrow the exception
            throw $e;
        }
    }


    public static function read($id): Message|string
    {
        $query = "SELECT * FROM Message WHERE messageId = :id LIMIT 0,1";
        $connection = DBConnector::getConnection();

        try {
            $sql = $connection->prepare($query);
            $sql->bindParam(':id', $id, PDO::PARAM_INT);
            $sql->execute();

            $row = $sql->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                return self::createMessageFromRow($row);
            } else {
                return 'Message was not found';
            }
        } catch (PDOException $e) {
            // You can log the error or handle it appropriately
            return 'Database error: ' . $e->getMessage(); // TODO just don't echo this later on
        } finally {
            $connection = null;  // Close the connection
        }
    }



    /*
     * Unsure if messages need to have "update" functionality
    public static function update($user): bool
    {
        $query = "UPDATE User SET 
                firstName=:firstName, 
                lastName=:lastName, 
                password=:password, 
                userType=:userType, 
                email=:email, 
                about=:about  
            WHERE userId=:userId";
        $sql = self::getSql($query, $user);
        $sql->bindValue(':userId', $user->getUserId(), PDO::PARAM_INT);

        return $sql->execute();
    }
    */

    public static function delete($id): bool
    {
        $query = "DELETE FROM Message WHERE messageId = :id";
        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);

        // Bind the ID parameter
        $sql->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the statement and return true if successful, false otherwise
        return $sql->execute();
    }




    public static function getMessagesBetweenUsers(int $senderId, int $receiverId): array {
        $connection = DBConnector::getConnection();

        $sql = "SELECT * FROM Message WHERE 
            (senderId = :senderId AND 
            receiverId = :receiverId) OR 
            (receiverId = :senderId AND 
             senderId = :receiverId);
        ";

        // Prepares the SQL
        $query = $connection->prepare($sql);
        $query->bindValue(':senderId', $senderId);
        $query->bindValue(':receiverId', $receiverId);

        // Executes the query
        $resultList = [];
        try {
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            // Appends Message objects if query results
            if ($results) {
                foreach ($results as $row) {
                    $resultList[] = self::createMessageFromRow($row);
                }
            }

        } catch (PDOException $exception) {
            echo "SQL Query fail: ";
        }

        return $resultList;
    }



    static public function getUserConversations(int $userId): array {
        $connection = DBConnector::getConnection();

        $sql = "
            SELECT
                IF(m.senderId = :userId, m.receiverId, m.senderId) AS conversationPartnerId,
                u.firstName,
                u.lastName,
                MAX(m.sentAt) AS lastMessageTime,
                MAX(m.isRead) AS lastMessageRead,
                (SELECT messageText FROM Message 
                        WHERE (senderId = conversationPartnerId AND receiverId = :userId) OR 
                              (receiverId = conversationPartnerId AND senderId = :userId) 
                        ORDER BY sentAt DESC LIMIT 1) AS lastMessage
            FROM
                Message m
            JOIN
                User u ON u.userId = IF(m.senderId = :userId, m.receiverId, m.senderId)
            WHERE
                m.senderId = :userId OR m.receiverId = :userId
            GROUP BY
                conversationPartnerId
            ORDER BY
                lastMessageRead ASC,
                lastMessageTime DESC;

        ";

        // Prepares the SQL
        $query = $connection->prepare($sql);
        $query->bindValue(':userId', $userId);

        // Executes the query
        try {
            $query->execute();
            $conversations = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            echo "SQL Query failed.";
        }

        return $conversations;
    }


    public static function getUnreadConversations(int $userId): array {
        $connection = DBConnector::getConnection();

        $sql = "
            SELECT COUNT(DISTINCT conversationPartnerId) AS unreadConversations
            FROM (
                SELECT
                    CASE
                        WHEN senderId = :userId THEN receiverId
                        WHEN receiverId = :userId THEN senderId
                    END AS conversationPartnerId,
                    MAX(sentAt) AS lastMessageTime
                FROM
                    Message
                WHERE
                    (senderId = :userId OR receiverId = :userId)
                    AND (
                        (receiverId = :userId AND isRead = 0)
                        OR (senderId = :userId AND receiverId != :userId)
                    )
                GROUP BY
                    conversationPartnerId
            ) AS subquery;
        ";

        // Prepares the SQL
        $query = $connection->prepare($sql);
        $query->bindValue(':userId', $userId);

        // Executes the query
        try {
            $query->execute();
            $conversations = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            echo "SQL Query failed.";
        }

        return $conversations;
    }

    private static function createMessageFromRow(array $row): Message
    {
        return new Message($row['messageId'],
            $row['senderId'],
            $row['receiverId'],
            new DateTime($row['sentAt']) ?? null,
            $row['messageText'],
            $row['isRead']
        );
    }
}
