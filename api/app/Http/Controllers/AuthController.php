<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Services\UserService;
use App\Http\Requests\AuthRequest;

class AuthController extends Controller

{
    private $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function login(AuthRequest $authRequest, AuthService $authService){
       return $authService->login($authRequest);
    }

    public function getUser(){
        $userWithCompany = $this->userService->getUserWithCompany();

        if (!$userWithCompany) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($userWithCompany);
    }

    public function logout(){
        auth()->logout();
        return response()->json(['message' => 'Deslogado com sucesso.']);
    }
}
