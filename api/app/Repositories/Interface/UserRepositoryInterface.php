<?php

namespace App\Repositories\Interface;
 
interface UserRepositoryInterface
{

    public function index();

    public function show(int $id);

    public function store($request);

    public function update($request);

    public function destory(int $id);
}
