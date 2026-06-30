<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Repositories\Interface\UserRepositoryInterface;
use App\Services\UserDetailsService;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class UserDetailsServiceTest extends TestCase
{
    protected UserRepositoryInterface $userRepository;
    protected UserDetailsService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->service = new UserDetailsService($this->userRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_signup_creates_user_and_returns_token(): void
    {
        $requestData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password1!',
        ];

        $user = Mockery::mock(User::class)->makePartial();
        $token = Mockery::mock(NewAccessToken::class);
        $token->plainTextToken = 'test-token-123';

        $this->userRepository
            ->shouldReceive('store')
            ->once()
            ->with($requestData)
            ->andReturn($user);

        $user->shouldReceive('createToken')
            ->once()
            ->with('main')
            ->andReturn($token);

        $response = $this->service->signup($requestData);

        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertEquals('test-token-123', $content['token']);
    }

    public function test_login_with_valid_credentials_returns_user_and_token(): void
    {
        $credentials = [
            'email' => 'john@example.com',
            'password' => 'Password1!',
        ];

        $user = Mockery::mock(User::class)->makePartial();
        $token = Mockery::mock(NewAccessToken::class);
        $token->plainTextToken = 'test-token-456';

        Auth::shouldReceive('attempt')
            ->once()
            ->with($credentials)
            ->andReturn(true);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($user);

        $user->shouldReceive('createToken')
            ->once()
            ->with('main')
            ->andReturn($token);

        $response = $this->service->login($credentials);

        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertEquals('test-token-456', $content['token']);
    }

    public function test_login_with_invalid_credentials_returns_error(): void
    {
        $credentials = [
            'email' => 'john@example.com',
            'password' => 'wrong-password',
        ];

        Auth::shouldReceive('attempt')
            ->once()
            ->with($credentials)
            ->andReturn(false);

        $response = $this->service->login($credentials);

        $this->assertEquals(422, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Provided email or password is incorrect', $content['message']);
    }

    public function test_logout_deletes_current_token(): void
    {
        $tokenMock = Mockery::mock();
        $tokenMock->shouldReceive('delete')->once();

        $user = Mockery::mock(User::class);
        $user->shouldReceive('currentAccessToken')
            ->once()
            ->andReturn($tokenMock);

        $response = $this->service->logout($user);

        $this->assertEquals(204, $response->getStatusCode());
    }

    public function test_first_or_create_google_login_returns_user_and_token(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);

        $user = Mockery::mock(User::class)->makePartial();
        $token = Mockery::mock(NewAccessToken::class);
        $token->plainTextToken = 'google-token-789';

        $this->userRepository
            ->shouldReceive('firstOrCreate')
            ->once()
            ->with($socialiteUser)
            ->andReturn($user);

        $user->shouldReceive('createToken')
            ->once()
            ->with('main')
            ->andReturn($token);

        $response = $this->service->firstOrCreateGoogleLogin($socialiteUser);

        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertEquals('google-token-789', $content['token']);
    }
}
