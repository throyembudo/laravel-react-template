<?php

namespace App\Repositories;

use App\Repositories\Interface\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{

    public function index()
    {
        return User::query()->orderBy('id', 'desc')->paginate(10);
    }

    public function show(int $id)
    {
        return User::find($id);
    }

    public function store($request, $details = false)
    {
        $request['password'] = bcrypt($request['password']);

        $user = User::create($request);

        return $user;
    }

    public function update($request)
    {
        $user = User::find($request['id']);

        if (isset($request['password'])) {
            $request['password'] = bcrypt($request['password']);
        }
        $user->update($request);

        return $user;

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
