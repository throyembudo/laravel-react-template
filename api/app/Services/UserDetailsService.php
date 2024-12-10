<?php

namespace App\Services;

use App\Services\Interface\UserDetailsServiceInterface;

use App\Repositories\Interface\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class UserDetailsService implements UserDetailsServiceInterface
{
    protected $user;

    public function __construct(UserRepositoryInterface $user)
    {
        $this->user = $user;
    }
    public function signup(array $request)
    {
        /** @var \App\Models\User $user */
        $user = $this->user->store($request);
        
        $token = $user->createToken('main')->plainTextToken;

        return response(compact('user', 'token'));
    }

    public function login(array $request)
    {
        if (!Auth::attempt($request)) {
            return response([
                'message' => 'Provided email or password is incorrect'
            ], 422);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $token = $user->createToken('main')->plainTextToken;
        
        return response(compact('user', 'token'));
    }

    public function logout(User $user)
    {
        $user->currentAccessToken()->delete();
    
        return response('', 204);
    }

    public function firstOrCreateGoogleLogin(SocialiteUser $socialiteUser)
    {
        $user = $this->user->firstOrCreate($socialiteUser);

        $token = $user->createToken('main')->plainTextToken;

        return response(compact('user', 'token'));
    }
}
