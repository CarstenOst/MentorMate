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


    /**
     * Get messages between two users, the sender and receiver.
     *
     * @param int $sender The ID of the user who sent the messages.
     * @param int $receiver The ID of the user who received the messages.
     * @return array An array of Message objects representing the messages between the users.
     */
    public static function getMessagesBetweenUsers(int $sender, int $receiver): array {
        $connection = DBConnector::getConnection();

        $sql = "SELECT * FROM Message WHERE 
            (senderId = :senderId AND 
            receiverId = :receiverId) OR 
            (receiverId = :senderId AND 
             senderId = :receiverId);
        ";

        // Prepares the SQL
        $query = $connection->prepare($sql);
        $query->bindValue(':senderId', $sender);
        $query->bindValue(':receiverId', $receiver);

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


    /**
     * Mark messages from the sender as read in a conversation between two users.
     *
     * @param int $receiver The ID of the user who received the messages.
     * @param int $sender The ID of the user who sent the messages.
     * @return void
     */
    public static function markMessagesAsRead(int $receiver, int $sender): void {
        $connection = DBConnector::getConnection();

        $sql = "UPDATE Message 
            SET isRead = 1
            WHERE (senderId = :senderId AND receiverId = :receiverId AND isRead = 0);
        ";

        // Prepares the SQL
        $query = $connection->prepare($sql);
        $query->bindValue(':receiverId', $receiver);
        $query->bindValue(':senderId', $sender);

        // Executes the query to mark messages as read
        try {
            $query->execute();
        } catch (PDOException $exception) {
            echo "SQL Query fail: ";
        }
    }



    /**
     * Get user conversations with details.
     *
     * This function retrieves conversations for a given user, including information about
     * the conversation partner, the last message time, and the last message text.
     *
     * @param int $userId The ID of the user for whom to retrieve conversations.
     * @return array An array containing details of user conversations.
     *               Each array includes: 'conversationPartnerId', 'firstName', 'lastName',
 *                                        'lastMessageTime', 'lastMessageRead', 'lastMessage'
     */
    static public function getUserConversations(int $userId): array {
        $connection = DBConnector::getConnection();

        $sql = "
            SELECT
                conversationPartner.userId AS conversationPartnerId,
                user.firstName,
                user.lastName,
                MAX(message.sentAt) AS lastMessageTime,
                MAX(message.isRead) AS lastMessageRead,
                message.messageText AS lastMessage
            FROM
                (
                    SELECT DISTINCT
                        CASE
                            WHEN senderId = :userId THEN receiverId
                            WHEN receiverId = :userId THEN senderId
                        END AS userId
                    FROM
                        Message
                    WHERE
                        senderId = :userId OR 
                        receiverId = :userId
                ) AS conversationPartner
            JOIN User user ON 
                conversationPartner.userId = user.userId
            LEFT JOIN (
                SELECT *
                FROM Message
                WHERE receiverId = :userId
            ) message ON 
                (message.senderId = :userId AND message.receiverId = conversationPartner.userId) OR 
                (message.receiverId = :userId AND message.senderId = conversationPartner.userId)
            GROUP BY
                conversationPartner.userId
            ORDER BY
                message.isRead ASC,
                lastMessageTime DESC;
        "; // TODO FIX THIS QUERY

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


    /**
     * Get the count of unread conversations for a specific user.
     *
     * This function retrieves the number of unread conversations for the given user.
     * A conversation is considered unread if there are messages where the user is the receiver
     * and the `isRead` attribute is set to 0.
     *
     * @param int $userId The ID of the user for whom to retrieve unread conversations.
     * @return array An associative array containing the count of unread conversations.
     */
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






    /**
     * @param string $query
     * @param $message
     * @return PDOStatement
     */
    private static function getSql(string $query, Message $message): PDOStatement
    {
        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);

        // Bind parameters using named parameters and bindValue
        $sql->bindValue(':senderId', $message->getSenderId());
        $sql->bindValue(':receiverId', $message->getReceiverId());
        $sql->bindValue(':sentAt', $message->getSentAt());
        $sql->bindValue(':messageText', $message->getMessageText());
        $sql->bindValue(':isRead', $message->getIsRead());

        return $sql;
    }


    /**
     * Creates a Message object from a database row.
     *
     * @param array $row The associative array representing a database row.
     * @return Message Returns a Message object created from the provided database row.
     */
    private static function createMessageFromRow(array $row): Message
    {
        $message = new Message();
        $message->setMessageId($row['messageId']);
        $message->setSenderId($row['senderId']);
        $message->setReceiverId($row['receiverId']);
        $message->setSentAt(new DateTime($row['sentAt']) ?? null);
        $message->setMessageText($row['messageText']);
        $message->setIsRead($row['isRead']);

        return $message;
    }
}
