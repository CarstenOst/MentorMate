<?php

namespace Core\Interfaces;

use Core\Entities\User;

interface IUserRepository
{
    public static function create(User $user);
    public static function read($id);
    public static function update($user);
    public static function delete($id);

}
