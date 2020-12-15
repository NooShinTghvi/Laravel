<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/t', [UserController::class, 'test']);
Route::get('/401', function () {
    return 'Unauthorized client';
})->name('UnauthorizedError');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/r', function () {
    return Auth()->user();
})->middleware('auth:api');
