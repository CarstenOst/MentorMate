<?php

namespace Repositories;
include_once '../../Core/Interfaces/IUserRepository.php';
include_once '../../Core/Entities/User.php';

use Entities\User;
use Interfaces\IUserRepository;
use PDO;

class UserRepository implements IUserRepository
{
    private User $user;

    public function create($user)
    {
        // TODO: Implement create() method.
    }


    public function read($conn, $id): User {
        $query = "SELECT * FROM User WHERE userId = ? LIMIT 0,1";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $user = new User();
        if ($row) {
            // map results to object properties
            $user->setUserType($row['userType']);
            $user->setFirstName($row['firstName']);
            $user->setEmail($row['email']);
            // TODO (map other properties)
        }
        return $user;
    }

    public function update($user)
    {
        // TODO: Implement update() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }
}
