<?php

namespace Interfaces;

interface IUserRepository
{
    public function create($user);
    public function read($id);
    public function update($user);
    public function delete($id);

}