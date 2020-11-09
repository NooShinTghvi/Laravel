<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GroupController;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckUser;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::prefix('group')->middleware([CheckUser::class])->group(function () {
    Route::post('', [GroupController::class, 'create'])->name('group.create');
    Route::post('add/{contactId}', [GroupController::class, 'addTo'])->name('group.add');
    Route::post('delete/{groupId}', [GroupController::class, 'deleteFrom'])->name('group.delete');
    Route::get('edit/{groupId}', [GroupController::class, 'edit'])->name('group.edit');
    Route::post('edit/{groupId}', [GroupController::class, 'saveChanges'])->name('group.save.changes');
    Route::post('remove/{groupId}', [GroupController::class, 'delete'])->name('group.remove');
});

Route::prefix('contact')->middleware([CheckUser::class])->group(function () {
    Route::post('', [ContactController::class, 'create'])->name('contact.create');
    Route::get('edit/{contactId}', [ContactController::class, 'edit'])->name('contact.edit');
    Route::post('edit/{contactId}', [ContactController::class, 'saveChanges'])->name('contact.save.changes');
    Route::post('change/image/{contactId}', [ContactController::class, 'changeImage'])->name('contact.change.image');
    Route::post('remove/{contactId}', [ContactController::class, 'delete'])->name('contact.remove');
});

Route::prefix('admin')->middleware([CheckAdmin::class])->group(function () {
    Route::post('', [AdminController::class, 'changeAccessingForContacts'])->name('admin.change.access');
});
