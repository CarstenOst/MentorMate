<?php
namespace Managers;
include_once '../../Infrastructure/Repositories/UserRepository.php';
include_once '../../Infrastructure/Database/DBConnector.php';

use Repositories\UserRepository;
use Database\DBConnector;

// TODO THIS IS ONLY TO CHECK THE DATABASE, AND IS NOT GOING TO BE LIKE THIS
$database = new DBConnector();
$db = $database->getConnection();

$request = new UserRepository();
$user = $request->read($db, 1);

echo "User Type: " . $user->getUserType();
echo "User Name: " . $user->getFirstName();
echo "User Mail: " . $user->getEmail();
