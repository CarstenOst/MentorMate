<?php
namespace Infrastructure\Repositories;

use Core\Entities\User;
use Core\Entities\Booking;
use Infrastructure\Database\DBConnector;
use Infrastructure\Repositories\ErrorCode;
use Interfaces\IBookingRepository;

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
                     studentId,
                     tutorId,
                     bookingTime, 
                     status) 
                VALUES (:studentId, 
                        :tutorId, 
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
            // Check if the error is due to a duplicate key on 'email'
            if ($e->getCode() == 23000 &&
                str_contains($e->getMessage(), 'Duplicate entry') &&
                str_contains($e->getMessage(), 'for key \'email\''))
            {
                // Handle duplicate email error
                echo "Error: The provided email already exists in the database!";
                return ErrorCode::DUPLICATE_EMAIL;
            } else {
                // Handle other errors or rethrow the exception
                throw $e;
            }
        }
    }



    /**
     * @param $id
     * @return User|String
     * @throws Exception
     */
    public static function read($id): User|String {

        $query = "SELECT * FROM User WHERE userId = :id LIMIT 0,1";
        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);
        $sql->bindParam(':id', $id, PDO::PARAM_INT); // To avoid SQL injection
        $sql->execute();

        $row = $sql->fetch(PDO::FETCH_ASSOC);

        $user = new User();
        if ($row) {
            // map results to object properties
            $user->setUserId($id); // Faster lookup, as we already have the id
            $user->setAbout($row['about']);
            $user->setEmail($row['email']);
            $user->setUserType($row['userType']);
            $user->setPassword($row['password']);
            $user->setLastName($row['lastName']);
            $user->setFirstName($row['firstName']);
            $user->setCreatedAt(new DateTime($row['createdAt']) ?? null); // Could cause exception
            $user->setUpdatedAt(new DateTime($row['updatedAt']) ?? null);
            return $user;
        }
        return 'User was not found';
    }

    public static function update($user): bool
    {
        $query = "UPDATE User SET 
                firstName=:firstName, 
                lastName=:lastName, 
                email=:email, 
                password=:password, 
                userType=:userType, 
                about=:about, 
                createdAt=:createdAt, 
                updatedAt=:updatedAt 
            WHERE userId=:userId";
        $sql = self::getSql($query, $user);
        $sql->bindValue(':userId', $user->getUserId(), PDO::PARAM_INT);
        $sql->bindValue(':createdAt', $user->getCreatedAt()->format('Y-m-d H:i:s'));
        $sql->bindValue(':updatedAt', $user->getUpdatedAt()->format('Y-m-d H:i:s'));

        // Execute the statement and return true if successful, false otherwise
        return $sql->execute();
    }


    public static function delete($id): bool
    {
        $query = "DELETE FROM User WHERE userId = :id";
        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);

        // Bind the ID parameter
        $sql->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the statement and return true if successful, false otherwise
        return $sql->execute();
    }


    /**
     * @param string $query
     * @param $user
     * @return PDOStatement
     */
    private static function getSql(string $query, $user): PDOStatement
    {
        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);

        // Bind parameters using named parameters and bindValue
        $sql->bindValue(':firstName', $user->getFirstName());
        $sql->bindValue(':lastName', $user->getLastName());
        $sql->bindValue(':email', $user->getEmail());
        $sql->bindValue(':password', $user->getPassword());
        $sql->bindValue(':userType', $user->getUserType());
        $sql->bindValue(':about', $user->getAbout());

        return $sql;
    }
}
