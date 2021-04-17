<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\StartPageController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/error', function () {
    return response('Plz Login first', 401);
})->name('login.Plz');

Route::get('main', [StartPageController::class, 'main']);
Route::get('news/{newsId}', [StartPageController::class, 'oneNews']);

Route::post('register', [AuthController::class, 'register']);
Route::get('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:user');

Route::get('information', [UserController::class, 'information'])->middleware('auth:user');
Route::patch('information', [UserController::class, 'updateInformation'])->middleware('user');
Route::get('counties/{provinceId}', [UserController::class, 'getCountiesByProvince']);
Route::get('cities/{provinceId}/{countyId}', [UserController::class, 'getCitiesByProvinceANDCounty']);


Route::prefix('cart')->middleware('auth:user')->name('cart.')->group(function () {
    Route::get('', [CartController::class, 'shoppingCartPreparation']);
    Route::put('exam/{examId}', [CartController::class, 'addExamToCart']);
    Route::delete('exam/{examId}', [CartController::class, 'deleteExamFromCart']);
});

Route::get('check/discount/code', [DiscountController::class, 'isValidCode'])->middleware('auth:user');

Route::post('buy', [TransactionController::class, 'buy'])->middleware('auth:user');
Route::get('payment-verify/{factorNumber}', [TransactionController::class, 'verifyInfo'])->middleware('auth:user')
->name('verify');
Route::get('transactions', [TransactionController::class, 'myTransaction'])->middleware('auth:user');

Route::prefix('exam')->name('exam.')->group(function () {
    Route::get('', [ExamController::class, 'showAll'])->name('show');
    Route::get('{examId}', [ExamController::class, 'detailOne'])->name('detail.one');
    Route::get('filter', [ExamController::class, 'filterExams'])->name('filter');
    Route::get('mine', [ExamController::class, 'myExams'])->middleware('auth:user')->name('mine');
});

Route::get('phases/{examId}', [ExamController::class, 'allPhases']);

Route::get('/entrance/{examId}/{phaseId}', [ExamController::class, 'entranceExam'])->middleware('auth:user');
Route::patch('/start/{examId}/{phaseId}', [ExamController::class, 'canUserStartTest'])->middleware('auth:user');

Route::post('/submit/{phaseId}', [ExamController::class, 'handle'])->middleware('auth:user');
Route::get('/answer/download/{phaseId}', [ExamController::class, 'downloadAnswer'])->middleware('auth:user');
Route::get('/question/download/{phaseId}', [ExamController::class, 'downloadQuestion'])->middleware('auth:user');
Route::get(' /report/{phaseId}', [EvaluationController::class, 'reportOfTest'])->middleware('auth:user');


Route::get('admin/report/{phaseId}/{userId}', 'AdminController@report');
