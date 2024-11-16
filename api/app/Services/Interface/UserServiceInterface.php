<?php

namespace App\Services\Interface;

interface UserServiceInterface
{
    
    public function listUser();

    public function createUser(array $request);

    public function updateUser(array $request);

    public function deleteUser(int $id);

    public function showUser(int $id);
}
