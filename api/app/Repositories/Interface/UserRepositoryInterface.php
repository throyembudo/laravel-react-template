<?php

declare(strict_types=1);

namespace App\Repositories\Interface;
use Laravel\Socialite\Contracts\User as SocialiteUser;
interface UserRepositoryInterface
{
    public function index();

    public function show(int $id);

    public function store($request);

    public function update($request);

    public function destroy(int $id);

    public function firstOrCreate(SocialiteUser $socialiteUser);
}
