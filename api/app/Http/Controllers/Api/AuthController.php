<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use Illuminate\Http\Request;
use App\Services\Interface\UserDetailsServiceInterface;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

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

    public function redirectToAuth(): JsonResponse
    {
        return response()->json([
            'url' => Socialite::driver('google')
            ->stateless()
            ->redirect()
            ->getTargetUrl(),
        ]);
    }

    public function handleAuthCallback()
    {
        try {
            /** @var SocialiteUser $socialiteUser */
            $socialiteUser = Socialite::driver('google')->stateless()->user();
        } catch (ClientException $e) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }

        return $this->userDetailsService->firstOrCreateGoogleLogin($socialiteUser);
    }
}
