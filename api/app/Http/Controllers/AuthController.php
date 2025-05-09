<?php

namespace App\Http\Controllers;

use App\Service\AuthService;
use App\Http\Requests\AuthRequest;

class AuthController extends Controller

{
    public function __construct() {}

    public function login(AuthRequest $authRequest, AuthService $authService){
       return $authService->login($authRequest);
    }

    public function logout(){
        auth()->logout();
        return response()->json(['message' => 'Deslogado com sucesso.']);
    }
}