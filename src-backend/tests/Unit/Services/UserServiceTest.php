<?php

namespace Tests\Unit\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Interface\UserRepositoryInterface;
use App\Services\UserService;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    protected UserRepositoryInterface $userRepository;
    protected UserService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->service = new UserService($this->userRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_list_user_returns_paginated_users(): void
    {
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $this->userRepository
            ->shouldReceive('index')
            ->once()
            ->andReturn($paginator);

        $result = $this->service->listUser();

        $this->assertSame($paginator, $result);
    }

    public function test_create_user_stores_and_returns_response(): void
    {
        $data = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'Password1!',
        ];

        $user = new User([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'created_at' => now(),
        ]);
        $user->id = 1;
        $user->created_at = now();

        $this->userRepository
            ->shouldReceive('store')
            ->once()
            ->with($data)
            ->andReturn($user);

        $response = $this->service->createUser($data);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_update_user_when_user_exists(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $user = new User([
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'created_at' => now(),
        ]);
        $user->id = 1;
        $user->created_at = now();

        $this->userRepository
            ->shouldReceive('show')
            ->once()
            ->with(1)
            ->andReturn($user);

        $this->userRepository
            ->shouldReceive('update')
            ->once()
            ->with($data)
            ->andReturn($user);

        $response = $this->service->updateUser($data);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_update_user_when_user_not_found(): void
    {
        $data = [
            'id' => 999,
            'name' => 'Ghost',
            'email' => 'ghost@example.com',
        ];

        $this->userRepository
            ->shouldReceive('show')
            ->once()
            ->with(999)
            ->andReturn(null);

        $response = $this->service->updateUser($data);

        $this->assertEquals(403, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertEquals('User not found. Unable to Update', $content['message']);
    }

    public function test_delete_user_calls_repository_destroy(): void
    {
        $this->userRepository
            ->shouldReceive('destroy')
            ->once()
            ->with(1);

        $this->service->deleteUser(1);

        // If no exception, the test passes
        $this->assertTrue(true);
    }

    public function test_show_user_returns_user_resource(): void
    {
        $user = new User([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'created_at' => now(),
        ]);
        $user->id = 1;

        $this->userRepository
            ->shouldReceive('show')
            ->once()
            ->with(1)
            ->andReturn($user);

        $result = $this->service->showUser(1);

        $this->assertInstanceOf(UserResource::class, $result);
    }
}
