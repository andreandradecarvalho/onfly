<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Validator;
use App\Models\User;


class AuthController extends Controller

{
    public function __construct() {
       $this->middleware();
    }

    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware(middleware: 'api', except: ['register', 'login']),
        ];
    }
    public function register(Request $r){
        $validator = Validator::make(
            $r->all(),
            [
                'name' => 'required',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|confirmed|min:6'
                ]
        );
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($r->password)]
        ));
        return response()->json([
            'message' => 'Registro realizado com sucesso.',
            'user' => $user
        ]);

    }

    public function login(Request $r){
        $validator = Validator::make(
            $r->all(),
            [
                'email' => 'required|email',
                'password' => 'required|string|min:6'
                ]
        );

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        if(!$token = auth()->attempt($validator->validated()))
        {
            return response()->json(['error' => 'Unauthorized 11'],401 );
        }

        return $this->createNewToken($token);

    }

    public function createNewToken($token){
        return response()->json([
            'data' => [
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60],
                'user' => auth()->user()
        ]);
    }

    public function getUser(){
        return response()->json(auth()->user());
    }

    public function logout(){
        auth()->logout();
        return response()->json([
            'message' => 'Deslogado com sucesso.'
        ]);
    }
}
