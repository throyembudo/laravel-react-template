<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use Illuminate\Http\Request;
use App\Services\Interface\UserDetailsServiceInterface;
use App\Models\User;
class AuthController extends Controller
{
    protected $userDetailsService;

    public function __construct(UserDetailsServiceInterface $userDetailsService)
    {
        $this->userDetailsService = $userDetailsService;
    }

    public function signup(SignupRequest $request)
    {
        $request = $request->validated();

        return $this->userDetailsService->signup($request);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        return $this->userDetailsService->login($credentials);
    }

    public function logout(Request $request)
    {
        return $this->userDetailsService->logout($request->user());
    }
}
