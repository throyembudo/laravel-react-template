<?php

namespace App\Services\Interface;
use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;

interface UserDetailsServiceInterface
{
    
    public function signup(array $request);

    public function login(array $request);

    public function logout(User $user);

    public function firstOrCreateGoogleLogin(SocialiteUser $socialiteUser);
}
