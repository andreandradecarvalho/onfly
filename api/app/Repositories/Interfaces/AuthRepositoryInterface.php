<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\AuthRequest;
use App\Models\User;

interface AuthRepositoryInterface
{
    public function login(AuthRequest $request);
    public function createToken($token);
}
