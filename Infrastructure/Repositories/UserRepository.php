<?php

namespace Infrastructure\Repositories;

use Infrastructure\Database\DBConnector;
use Core\Interfaces\IUserRepository;
use Core\Entities\User;
use Exception;
use DateTime;
use PDO;

class UserRepository implements IUserRepository
{

    public static function create($user)
    {
        // TODO: Implement create() method.
    }


    /**
     * @param $id
     * @return User|String
     * @throws Exception
     */
    public static function read($id): User|String {

        $query = "SELECT * FROM User WHERE userId = ? LIMIT 0,1";
        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);
        $sql->bindParam(1, $id, PDO::PARAM_INT); // To avoid SQL injection
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

    public static function update($user)
    {
        // TODO: Implement update() method.
    }

    public static function delete($id)
    {
        // TODO: Implement delete() method.
    }
}
