<?php

namespace App\Services;

use App\Repositories\Interfaces\AuthRepositoryInterface;


class AuthService implements AuthRepositoryInterface
{
   public function __construct(){}

    public function login($request)
    {
        $input = $request->validated();
        $credentials = request(['email', 'password']);

        if(!$token = auth()->attempt($credentials)){
           return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createToken($token);

    }

    public function createToken($token){
        return response()->json([
            'data' => [
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60],
                'user' => ['id' => auth()->user()->id, 'name' => auth()->user()->name]
        ]);
    }


}
