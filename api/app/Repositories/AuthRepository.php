<?php

namespace App\Repositories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\AuthRepositoryInterface;

class AuthRepository implements AuthRepositoryInterface
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function login($request){}
    public function createToken(User $user){}

}
