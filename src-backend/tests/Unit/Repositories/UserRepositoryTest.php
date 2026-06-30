<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new UserRepository();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_index_returns_paginated_users(): void
    {
        User::factory()->count(15)->create();

        $result = $this->repository->index();

        $this->assertCount(10, $result->items());
        $this->assertEquals(15, $result->total());
    }

    public function test_index_returns_users_ordered_by_id_desc(): void
    {
        $users = User::factory()->count(3)->create();

        $result = $this->repository->index();
        $items = $result->items();

        $this->assertGreaterThan($items[1]->id, $items[0]->id);
        $this->assertGreaterThan($items[2]->id, $items[1]->id);
    }

    public function test_show_returns_user_by_id(): void
    {
        $user = User::factory()->create();

        $result = $this->repository->show($user->id);

        $this->assertNotNull($result);
        $this->assertEquals($user->id, $result->id);
        $this->assertEquals($user->email, $result->email);
    }

    public function test_show_returns_null_for_nonexistent_user(): void
    {
        $result = $this->repository->show(999);

        $this->assertNull($result);
    }

    public function test_store_creates_user_with_hashed_password(): void
    {
        $data = [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'PlainPassword1!',
        ];

        $user = $this->repository->store($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('New User', $user->name);
        $this->assertEquals('new@example.com', $user->email);
        // Password should be hashed, not plain
        $this->assertNotEquals('PlainPassword1!', $user->password);
        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
    }

    public function test_update_modifies_user_data(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $data = [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $result = $this->repository->update($data);

        $this->assertEquals('Updated Name', $result->name);
        $this->assertEquals('updated@example.com', $result->email);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_update_hashes_password_when_provided(): void
    {
        $user = User::factory()->create();

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'NewPassword1!',
        ];

        $result = $this->repository->update($data);

        $this->assertNotEquals('NewPassword1!', $result->password);
    }

    public function test_destroy_deletes_existing_user(): void
    {
        $user = User::factory()->create();

        $response = $this->repository->destroy($user->id);

        $this->assertEquals(204, $response->getStatusCode());
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_destroy_returns_404_for_nonexistent_user(): void
    {
        $response = $this->repository->destroy(999);

        $this->assertEquals(404, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertEquals('User not found', $content['message']);
    }

    public function test_first_or_create_creates_new_user_from_socialite(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getEmail')->andReturn('google@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Google User');
        $socialiteUser->shouldReceive('getId')->andReturn('google-id-123');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://avatar.url/photo.jpg');

        $user = $this->repository->firstOrCreate($socialiteUser);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('google@example.com', $user->email);
        $this->assertEquals('Google User', $user->name);
        $this->assertEquals('google-id-123', $user->google_id);
        $this->assertDatabaseHas('users', ['email' => 'google@example.com']);
    }

    public function test_first_or_create_returns_existing_user(): void
    {
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getEmail')->andReturn('existing@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('New Name');
        $socialiteUser->shouldReceive('getId')->andReturn('google-id-456');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://avatar.url/new.jpg');

        $user = $this->repository->firstOrCreate($socialiteUser);

        $this->assertEquals($existingUser->id, $user->id);
        // Name should not be updated for existing user
        $this->assertEquals($existingUser->name, $user->name);
    }
}
