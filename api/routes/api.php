<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserRegistrationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/healthcheck', function (Request $request) {
    return response()->json(['ok'],200);
});

Route::group([ 'prefix' => 'v1', 'as' => 'v1.'], function () {
    Route::get('/', function () { return response()->json(['error' => ['message' => ['Não há nada aqui, além de um imenso vazio.']]], 422); })->name('nothing');
    Route::post('/', function () { return response()->json(['error' => ['message' => ['Não há nada aqui, além de um imenso vazio.']]], 422); })->name('nothing');

    /* AUTH */
    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::post('register', [UserRegistrationController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);

        Route::group(['middleware' => 'api'], function () {
            Route::post('refresh', [AuthController::class, 'refreshToken']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('user', [AuthController::class, 'getUser']);
        });
    });
});
