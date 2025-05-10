<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserRegistrationController;
use App\Http\Controllers\CompanyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAuthorization;
use App\Http\Controllers\UserController;


Route::get('/healthcheck', function (Request $request) {
    return response()->json(['ok'],200);
});

Route::group([ 'prefix' => 'v1', 'as' => 'v1.'], function () {
    Route::get('/', function () { return response()->json(['error' => ['message' => ['Não há nada aqui, além de um imenso vazio.']]], 422); })->name('nothing');
    Route::post('/', function () { return response()->json(['error' => ['message' => ['Não há nada aqui, além de um imenso vazio.']]], 422); })->name('nothing');

    Route::get('/healthcheck', function (Request $request) {
        return response()->json(['ok'],200);
    });

    /* AUTH */
    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::post('register', [UserRegistrationController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);

        Route::group(['middleware' => ['api',CheckAuthorization::class]], function () {
            Route::post('refresh', [AuthController::class, 'refreshToken']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('user', [AuthController::class, 'getUser']);
            Route::get('me', [AuthController::class, 'getUser']);
        });
    });

    /* COMPANIES */
   Route::group(['prefix' => 'companies', 'as' => 'companies.', 'middleware' => ['api', CheckAuthorization::class]], function () {
       Route::get('/', [CompanyController::class, 'index']);
       Route::post('/', [CompanyController::class, 'store']); // Add this line
       Route::get('/{id}', [CompanyController::class, 'show']);
       Route::put('/{id}', [CompanyController::class, 'update']);
       Route::delete('/{id}', [CompanyController::class, 'destroy']);
   });

   /* USERS */
   Route::group(['prefix' => 'users', 'as' => 'users.', 'middleware' => ['api', CheckAuthorization::class]], function () {
       Route::get('/', [UserController::class, 'index']);
       Route::post('/', [UserController::class, 'store']); // Add this line
       Route::get('/{id}', [UserController::class, 'show']);
       Route::put('/{id}', [UserController::class, 'update']);
       Route::delete('/{id}', [UserController::class, 'destroy']);
   });
});
