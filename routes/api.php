<?php

use App\Http\Controllers\JwtAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('login', [JwtAuthController::class, 'login']);
Route::post('register', [JwtAuthController::class, 'register']);
Route::get('error', function (){
    return response()->json(['error' => 'Unauthorized'], 401);
})->name('error');
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('logout', [JwtAuthController::class, 'logout']);
    Route::get('refresh', [JwtAuthController::class, 'refresh']);
    Route::get('me', [JwtAuthController::class, 'me']);
});
