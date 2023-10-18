<?php

namespace Core\Interfaces;

interface IUserRepository
{
    public static function create($user);
    public static function read($id);
    public static function update($user);
    public static function delete($id);

}
