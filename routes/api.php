<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\StartPageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/error', function () {
    return response('Plz Login first', 401);
})->name('login.Plz');
Route::get('/t2', [AuthController::class, 't2'])->middleware('auth:user');
Route::get('/t', [AuthController::class, 'user'])->middleware('auth:user');

Route::get('main', [StartPageController::class, 'main'])->name('main');
Route::get('news/{newsId}', [StartPageController::class, 'oneNews'])->name('one.news');

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:user')->name('logout');

Route::get('information', [UserController::class, 'information'])->middleware('auth:user')->name('information');
Route::patch('information', [UserController::class, 'updateInformation'])->middleware('user')->name('information.update');
Route::get('counties/{provinceId}', [UserController::class, 'getCountiesByProvince'])->name('counties');
Route::get('cities/{provinceId}/{countyId}', [UserController::class, 'getCitiesByProvinceANDCounty'])->name('cities');


Route::prefix('cart')->middleware('auth:user')->name('cart.')->group(function () {
    Route::get('', [CartController::class, 'shoppingCartPreparation']);
    Route::patch('/add/exam/{examId}', [CartController::class, 'addExamToCart'])->name('add.exam');
    Route::patch('/delete/exam/{examId}', [CartController::class, 'deleteExamFromCart'])->name('delete.exam');
});

Route::get('check/discount/code', [DiscountController::class, 'isValidCode'])->middleware('auth:user')->name('check.discount');

Route::prefix('exam')->name('exam.')->group(function () {
    Route::get('', [ExamController::class, 'showAll'])->name('show');
    Route::get('{examId}', [ExamController::class, 'detailOne'])->name('detail.one');
    Route::get('phases/{examId}', [ExamController::class, 'allPhases'])->name('all.phases');
    Route::post('filter', [ExamController::class, 'filterExams'])->name('filter');

    Route::get('mine', [ExamController::class, 'myExams'])->middleware('auth:user')->name('mine');
    Route::get('/entrance/{examId}/{phaseId}', [ExamController::class, 'entranceExam'])->middleware('auth:user')->name('entrance');
    Route::get('/start/{examId}/{phaseId}', [ExamController::class, 'canUserStartTest'])->middleware('auth:user')->name('start.test');
    Route::post('/submit/test/{phaseId}', [ExamController::class, 'handle'])->middleware('auth:user')->name('submit.test');

    Route::get('/answer/download/{phaseId}', [ExamController::class, 'downloadAnswer'])->middleware('auth:user')->name('answer.download');
    Route::get('/question/download/{phaseId}', [ExamController::class, 'downloadQuestion'])->middleware('auth:user')->name('question.download');
    Route::get(' /report/{phaseId}', [EvaluationController::class, 'reportOfTest'])->middleware('auth:user')->name('report');
});

Route::prefix('buyable')->middleware('user')->name('buyable . ')->group(function () {
//    Route::get(' / show / info', 'TransactionController@viewPurchaseInformation')->name('buyable . buy_showInfo'); //todo get rewrite to post
    Route::get('buy', 'TransactionController@buy')->name('buy'); //todo get rewrite to post
    Route::get('verify/{factorNumber}', 'TransactionController@verifyInfo');
    Route::get('user/transaction', 'TransactionController@myTransaction')->name('user . transaction');
});

Route::prefix('admin')->name('admin . ')->group(function () {
    Route::get(' / report /{
        phaseId}/{
        userId}', 'AdminController@report');
});


Route::view(' / basket', 'buyable::basket');


