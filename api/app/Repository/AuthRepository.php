<?php

namespace App\Repository;

use App\Contracts\AuthRepositoryInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuthRepository
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function login($request)
    {

    }
    public function createToken(User $user){

    }

}