<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::prefix('')->group(function () {
    Route::post('group', [GroupController::class, 'create'])->name('group.create');
});

Route::prefix('contact')->group(function () {
    Route::post('', [ContactController::class, 'create'])->name('contact.create');
});
