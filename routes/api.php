<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Todos\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::group(['prefix'=> 'auth'], function () {
Route::post('/register', [AuthController::class , 'register']);
Route::post('/login', [AuthController::class , 'login']);
Route::get('/me', [AuthController::class ,'me']);
Route::post('/refresh', [AuthController::class ,'refresh']);
Route::post('/logout', [AuthController::class ,'logout']);
});

Route::group(['middleware'=> 'auth:api'], function () {
    Route::get('/todo', [TodoController::class ,'index']);
    Route::post('/todo', [TodoController::class ,'store']);
});