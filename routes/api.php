<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/401', function () {
    return 'Unauthorized client';
})->name('UnauthorizedError');
Route::get('/t', [UserController::class, 'test']);


Route::prefix('user')/*->middleware('auth:api')*/ ->name('user.')->group(function () {
    Route::get('/', function () {
        return Auth()->user();
    });
    Route::get('/charge', [UserController::class, 'accountCharging'])->name('charge');
});

Route::prefix('restaurant')/*->middleware('auth')*/ ->name('restaurant.')->group(function () {
    Route::get('/', [RestaurantController::class, 'findRestaurantAroundUser'])->name('around');
    Route::get('/find/{idOrName}', [RestaurantController::class, 'findRestaurant'])->name('find');
});

Route::prefix('cart')/*->middleware('auth')*/ ->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'getCart'])->name('get');
    Route::get('/add/{restaurantId}/{foodId}', [CartController::class, 'addFood'])->name('addFood');
    Route::get('/submit', [CartController::class, 'submit'])->name('submit');
});


