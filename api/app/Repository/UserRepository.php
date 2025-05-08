<?php

namespace App\Repository;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function create(Request $request)
    {
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->save();
        return $user;
    }

}