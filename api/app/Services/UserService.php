<?php

namespace App\Services;

use App\Services\Interface\UserServiceInterface;
use App\Repositories\Interface\UserRepositoryInterface;
use App\Http\Resources\UserResource;

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
        $data = $this->user->store($data);

        return response(new UserResource($data) , 201);
    }

    public function updateUser(array $data)
    {
        $user = $this->user->show($data['id']);
        if ($user) {
            $data = $this->user->update($data);

            return response(new UserResource($data) , 200);
        } else {
            return response()->json(['message' => 'User not found. Unable to Update'], 403);
        }
    }

    public function deleteUser(int $id)
    {
        return $this->user->destroy($id);
    }

    public function showUser(int $id)
    {
        $data =  $this->user->show($id);

        return new UserResource($data);
    }
}
