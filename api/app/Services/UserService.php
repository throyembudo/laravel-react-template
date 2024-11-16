<?php

namespace App\Services;

use App\Services\Interface\UserServiceInterface;
use App\Repositories\Interface\UserRepositoryInterface;
// app/Services/UserService.php
class UserService implements UserServiceInterface
{
    protected $user;

    public function __construct(UserRepositoryInterface $user)
    {
        $this->user = $user;
    }

    public function listUser()
    {
        return $this->user->index();
    }

    public function createUser(array $data)
    {
        return $this->user->store($data);
    }

    public function updateUser(array $data)
    {
        return $this->user->update($data);
    }

    public function deleteUser(int $id)
    {
        return $this->user->destory($id);
    }

    public function showUser(int $id)
    {
        return $this->user->show($id);
    }
}
