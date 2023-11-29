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

        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);
        $sql->bindValue(':tutorId', $booking->getTutorId());
        $sql->bindValue(':bookingTime', $booking->getBookingTime()->format('Y-m-d H:i:s'));
        $sql->bindValue(':status', $booking->getStatus());

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
            $booking->setBookingTime(new DateTime($row['bookingTime']) ?? null);
            $booking->setStatus($row['status']);
            $booking->setCreatedAt(new DateTime($row['createdAt']) ?? null); // Could cause exception
            $booking->setUpdatedAt(new DateTime($row['updatedAt']) ?? null);
            return $booking;
        }
        return 'Booking was not found';
    }

    /**
     * Updates a booking in the database.
     *
     * @param Booking $booking The Booking object to update.
     * @return bool Returns true if the update is successful, false otherwise.
     * @throws PDOException Throws a PDOException if there is an error with the SQL query.
     */
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
            WHERE bookingId=:bookingId";
        $sql = self::getSql($query, $booking);
        $sql->bindValue(':bookingId', $booking->getBookingId());
        $sql->bindValue(':studentId', $booking->getStudentId());
        $sql->bindValue(':tutorId', $booking->getTutorId());
        $sql->bindValue(':bookingTime', $booking->getBookingTime()->format('Y-m-d H:i:s'));
        $sql->bindValue(':status', $booking->getStatus());
        $sql->bindValue(':createdAt', $booking->getCreatedAt()->format('Y-m-d H:i:s'));
        $sql->bindValue(':updatedAt', $booking->getUpdatedAt()->format('Y-m-d H:i:s'));

        // Execute the statement and return true if successful, false otherwise
        return $sql->execute();
    }


    /**
     * Deletes a booking from the database based on the booking ID.
     *
     * @param int $id The booking ID to be deleted.
     * @return bool Returns true if the deletion is successful, false otherwise.
     * @throws PDOException Throws a PDOException if there is an error with the SQL query.
     */
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
        $sql->bindValue(':bookingTime', $booking->getBookingTime()->format('Y-m-d H:i:s'));
        $sql->bindValue(':status', $booking->getStatus());
        $sql->bindValue(':createdAt', $booking->getCreatedAt()->format('Y-m-d H:i:s'));
        $sql->bindValue(':updatedAt', $booking->getUpdatedAt()->format('Y-m-d H:i:s'));

        return $sql;
    }


    /**
     * Creates a Booking object from a database row.
     *
     * @param array $row The associative array representing a database row.
     * @return Booking Returns a Booking object created from the provided database row.
     */
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

    /**
     * Retrieves bookings for a specific date.
     *
     * @param DateTime $date The date for which to retrieve bookings.
     * @return array Returns an array of Booking objects for the specified date.
     */
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


    /**
     * Retrieves upcoming bookings for a specific student by querying the database
     * to fetch bookings for that student, that occur on or after a specified date.
     *
     * @param DateTime $currentDate The starting date from which to retrieve bookings.
     * @param int      $studentId   The unique identifier of the student.
     *
     * @return array An array of Booking objects representing the student's bookings.
     */
    public static function getStudentBookings(DateTime $currentDate, int $studentId): array {
        $connection = DBConnector::getConnection();
        // Sets start and end -Date to be the hour interval from 08:00:00 to 23:59:59
        $startDate = new DateTime($currentDate->format('Y-m-d') . ' 08:00:00');
        $sql = "SELECT * FROM Booking WHERE 
            studentId = :studentId AND
            bookingTime >= :startDate;
        ";

        // Prepares the SQL
        $query = $connection->prepare($sql);
        $query->bindValue(':studentId', $studentId);
        $query->bindValue(':startDate', $startDate->format('Y-m-d H:i:s'));

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


    /**
     * Retrieves upcoming bookings for a specific tutor by querying the database
     * to fetch bookings for that tutor, that occur on or after a specified date.
     *
     * @param DateTime $currentDate The starting date from which to retrieve bookings.
     * @param int      $tutorId   The unique identifier of the tutor.
     *
     * @return array An array of Booking objects representing the tutor's bookings.
     */
    public static function getTutorBookings(DateTime $currentDate, int $tutorId): array {
        $connection = DBConnector::getConnection();
        // Sets start and end -Date to be the hour interval from 08:00:00 to 23:59:59
        $startDate = new DateTime($currentDate->format('Y-m-d') . ' 08:00:00');
        $sql = "SELECT * FROM Booking WHERE 
            tutorId = :tutorId AND
            bookingTime >= :startDate;
        ";

        // Prepares the SQL
        $query = $connection->prepare($sql);
        $query->bindValue(':tutorId', $tutorId);
        $query->bindValue(':startDate', $startDate->format('Y-m-d H:i:s'));

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


    /**
     * Retrieves bookings within time interval 08:00 and 23:59:59 for a specific tutor by
     * querying the database to fetch bookings for that tutor, that occur on the specified date.
     *
     * @param DateTime $currentDate The date (08:00 and 23:59:59) from which to retrieve bookings.
     * @param int      $tutorId   The unique identifier of the tutor.
     *
     * @return array An array of Booking objects representing the tutor's bookings.
     */
    public static function getTutorBookingsForDate(DateTime $startDate, int $tutorId): array {
        $connection = DBConnector::getConnection();
        // Sets start and end -Date to be the hour interval from 'Y-m-d 08:00:00' to 'Y-m-d 23:59:59'
        $startDate = new DateTime($startDate->format('Y-m-d') . ' 08:00:00');
        $endDate = new DateTime($startDate->format('Y-m-d') . ' 23:59:59');
        $sql = "SELECT * FROM Booking WHERE 
            tutorId = :tutorId AND
            bookingTime >= :startDate AND
            bookingTime < :endDate;
        ";

        // Prepares the SQL
        $query = $connection->prepare($sql);
        $query->bindValue(':tutorId', $tutorId);
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
