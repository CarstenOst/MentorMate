<?php
namespace Infrastructure\Repositories;

use Core\Entities\Booking;
use Infrastructure\Database\DBConnector;
use Core\Interfaces\IBookingRepository;
use PDOException;
use PDOStatement;
use Exception;
use DateTime;
use PDO;

class BookingRepository implements IBookingRepository
{
    /**
     * @param Booking $booking
     * @return int
     * @throws Exception
     */
    public static function create(Booking $booking): int
    {
        $query = "INSERT INTO Booking (
                     tutorId,
                     bookingTime, 
                     status) 
                VALUES (:tutorId, 
                        :bookingTime, 
                        :status);

            SELECT LAST_INSERT_ID() as id;
        ";

        $sql = self::getSql($query, $booking);

        try {
            // Execute the statement
            $sql->execute();
            return $sql->fetch(PDO::FETCH_ASSOC) ?? -1 ;

        } catch (PDOException $e) {
            // Handle other errors or rethrow the exception
            throw $e;
        }
    }



    /**
     * @param $id
     * @return Booking|String
     * @throws Exception
     */
    public static function read($id): Booking|String {

        $query = "SELECT * FROM Booking WHERE bookingId = :id LIMIT 0,1";
        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);
        $sql->bindParam(':id', $id, PDO::PARAM_INT); // To avoid SQL injection
        $sql->execute();

        $row = $sql->fetch(PDO::FETCH_ASSOC);

        $booking = new Booking();
        if ($row) {
            // map results to object properties
            $booking->setBookingId($id); // Faster lookup, as we already have the id
            $booking->setStudentId($row['studentId']);
            $booking->setTutorId($row['tutorId']);
            $booking->setBookingTime($row['bookingTime']);
            $booking->setStatus($row['status']);
            $booking->setCreatedAt(new DateTime($row['createdAt']) ?? null); // Could cause exception
            $booking->setUpdatedAt(new DateTime($row['updatedAt']) ?? null);
            return $booking;
        }
        return 'Booking was not found';
    }

    public static function update($booking): bool
    {
        $query = "UPDATE Booking SET 
                bookingId=:bookingId, 
                studentId=:studentId, 
                tutorId=:tutorId, 
                bookingTime=:bookingTime, 
                status=:status,
                createdAt=:createdAt, 
                updatedAt=:updatedAt 
            WHERE userId=:userId";
        $sql = self::getSql($query, $booking);
        $sql->bindValue(':bookingId', $booking->getBookingId(), PDO::PARAM_INT);
        $sql->bindValue(':createdAt', $booking->getCreatedAt()->format('Y-m-d H:i:s'));
        $sql->bindValue(':updatedAt', $booking->getUpdatedAt()->format('Y-m-d H:i:s'));

        // Execute the statement and return true if successful, false otherwise
        return $sql->execute();
    }


    public static function delete($id): bool
    {
        $query = "DELETE FROM Booking WHERE bookingId = :id";
        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);

        // Bind the ID parameter
        $sql->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the statement and return true if successful, false otherwise
        return $sql->execute();
    }


    /**
     * @param string $query
     * @param $booking
     * @return PDOStatement
     */
    private static function getSql(string $query, Booking $booking): PDOStatement
    {
        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);

        // Bind parameters using named parameters and bindValue
        $sql->bindValue(':bookingId', $booking->getBookingId());
        $sql->bindValue(':studentId', $booking->getStudentId());
        $sql->bindValue(':tutorId', $booking->getTutorId());
        $sql->bindValue(':bookingTime', $booking->getBookingTime());
        $sql->bindValue(':status', $booking->getStatus());
        $sql->bindValue(':createdAt', $booking->getCreatedAt());
        $sql->bindValue(':updatedAt', $booking->getUpdatedAt());

        return $sql;
    }


    public static function makeBookingFromRow($row): Booking
        {
            $booking = new Booking();
            $booking->setBookingId($row['bookingId']);
            $booking->setStudentId($row['studentId']);
            $booking->setTutorId($row['tutorId']);
            $booking->setBookingTime(new DateTime($row['bookingTime']) ?? null);
            $booking->setStatus($row['status']);
            $booking->setCreatedAt(new DateTime($row['createdAt']) ?? null);
            $booking->setUpdatedAt(new DateTime($row['updatedAt']) ?? null);

            return $booking;
        }

    public static function getBookingForDate(DateTime $date): array {
        $connection = DBConnector::getConnection();
        // Sets start and end -Date to be the hour interval from 08:00:00 to 23:59:59
        $startDate = new DateTime($date->format('Y-m-d') . ' 08:00:00');
        $endDate = new DateTime($date->format('Y-m-d') . ' 23:59:59');
        $sql = "SELECT * FROM Booking WHERE 
            bookingTime >= :startDate AND 
            bookingTime < :endDate;
        ";

        // Prepares the SQL
        $query = $connection->prepare($sql);
        $query->bindValue(':startDate', $startDate->format('Y-m-d H:i:s'));
        $query->bindValue(':endDate', $endDate->format('Y-m-d H:i:s'));

        // Executes the query
        $resultList = [];
        try {
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            // Appends Booking objects if query results
            if ($results) {
                foreach ($results as $row) {
                    $resultList[] = self::makeBookingFromRow($row);
                }
            }

        } catch (PDOException $exception) {
            echo "SQL Query fail: " . $exception->getMessage();
        }

        return $resultList;
    }

}
