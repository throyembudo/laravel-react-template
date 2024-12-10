<?php

namespace App\Services\Interface;
use App\Models\User;

interface UserDetailsServiceInterface
{
    
    public function signup(array $request);

    public function login(array $request);

    public function logout(User $user);
}
