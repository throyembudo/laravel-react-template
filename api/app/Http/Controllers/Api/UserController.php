<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\{
    StoreUserRequest,
    UpdateUserRequest
};
use App\Http\Resources\UserResource;
use App\Services\Interface\UserServiceInterface;

class UserController extends Controller
{

    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        return UserResource::collection($this->userService->listUser());
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        return $this->userService->createUser($data);
    }

    public function show(int $id)
    {
        return $this->userService->showUser($id);
    }

    public function update(UpdateUserRequest $request)
    {
        $request = $request->validated();
        
        return $this->userService->updateUser($request);
    }

    public function destroy(int $id)
    {
        return $this->userService->deleteUser($id);
    }
}
