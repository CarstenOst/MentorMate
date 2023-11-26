<?php

namespace Repositories;

use Infrastructure\Database\DBConnector;
use Core\Interfaces\IUserRepository;
use Core\Entities\User;
use PDOException;
use PDOStatement;
use Exception;
use DateTime;
use PDO;
class MessageRepository
{

    public static function create(): bool
    {
        return false;
    }
}
