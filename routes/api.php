<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StartPageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/error', function () {
    return response('Plz Login first', 401);
})->name('login.Plz');
Route::get('/t', function () {
    return response('Test');
})->middleware('auth:user');

Route::get('main', [StartPageController::class, 'main'])->name('main');
Route::get('news/{news_id}', [StartPageController::class, 'oneNews'])->name('one.news');

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:user')->name('logout');

Route::get('information', [UserController::class, 'information'])->middleware('auth:user')->name('information');
Route::patch('information', [UserController::class, 'updateInformation'])->middleware('user')->name('information.update');
Route::get('counties/{province_id}', [UserController::class, 'getCountiesByProvince'])->name('counties');
Route::get('cities/{province_id}/{county_id}', [UserController::class, 'getCitiesByProvinceANDCounty'])->name('cities');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/report/{phaseId}/{userId}', 'AdminController@report');
});

Route::prefix('start/page')->name('start.page.')->group(function () {

});

Route::prefix('buyable')->middleware('user')->name('buyable.')->group(function () {
    Route::post('apply/discount/code', 'DiscountController@isValidCode')->name('discount.apply');
//    Route::get('/show/info', 'TransactionController@viewPurchaseInformation')->name('buyable.buy_showInfo'); //todo get rewrite to post
    Route::get('buy', 'TransactionController@buy')->name('buy'); //todo get rewrite to post
    Route::get('verify/{factorNumber}', 'TransactionController@verifyInfo');
    Route::get('user/transaction', 'TransactionController@myTransaction')->name('user.transaction');
});

Route::view('/basket', 'buyable::basket');

Route::prefix('cart')->middleware('user')->name('cart.')->group(function () {
    Route::get('/buy/immediate/exam/{examId}', 'CartController@addImmediateExam')->name('addImmediate');
    Route::get('/add/exam/{examId}', 'CartController@addExamToCart')->name('addExam'); //todo change get to post
    Route::get('/delete/exam/{examId}', 'CartController@deleteExamFromCart')->name('deleteExam'); //todo change get to post
    Route::get('/show/exams', 'CartController@showExamsOnCart')->name('showProducts');

    /*Route::get('/', function (){
        dd(session()->all());
    });*/

});

Route::prefix('exam')->name('exam.')->group(function () {
    Route::get('/all', 'ExamController@showAllExams')->name('show');
    Route::get('/detail/one/{examId}', 'ExamController@detailExam')->name('detail.one');
    Route::get('/entrance/{examId}/{phaseId}', 'ExamController@entranceExam')->name('entrance');
    Route::get('/show/all/phases/{examId}', 'ExamController@showAllPhases')->name('show.all.phases');
    Route::post('/filter', 'ExamController@filterExams')->name('filter');

    Route::get('/answer/download/{phaseId}', 'ExamController@downloadAnswer')->name('answer.download');
    Route::get('/question/download/{phaseId}', 'ExamController@downloadQuestion')->name('question.download');

    Route::get('/my', 'ExamController@myExams')->name('my')->middleware('user');
    Route::get('/start/test/{examId}/{phaseId}', 'ExamController@canUserStartTest')->name('start.test')->middleware('user');
    Route::post('/submit/test/{phaseId}', 'ExamController@handle')->name('submit.test')->middleware('user');
    Route::get('/report/all', 'EvaluationController@reportAll')->name('report.all')->middleware('user');
    Route::get('/report/{phaseId}', 'EvaluationController@reportOfTest')->name('report')->middleware('user');

//    Route::get('/test', 'ExamController@export');
});


