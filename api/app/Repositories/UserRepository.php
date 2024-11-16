<?php

namespace App\Repositories;

use App\Repositories\Interface\UserRepositoryInterface;
use App\Models\User;
use App\Http\Resources\UserResource;

class UserRepository implements UserRepositoryInterface
{

    public function index()
    {
        return User::query()->orderBy('id', 'desc')->paginate(10);
    }

    public function show(int $id)
    {
        return new UserResource(User::find($id));
    }

    public function store($request)
    {
        $request['password'] = bcrypt($request['password']);

        $user = User::create($request);

        return response(new UserResource($user) , 201);
    }

    public function update($request)
    {
        $user = User::find($request['id']);

        if ($user) {
            if (isset($request['password'])) {
                $request['password'] = bcrypt($request['password']);
            }
            $user->update($request);

            return  response(new UserResource($user) , 200);
        } else {
            return response()->json(['message' => 'User not found. Unable to Update'], 403);
        }
    }

    public function destory(int $id)
    {
        $user = User::find($id);

        if ($user) {
            $user->delete($id); 
            
            return response("", 204);
        } else {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

}
