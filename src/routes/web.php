<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::post('student-create', [\App\Http\Controllers\Api\PaymentController::class, 'studentStore'])->name('student.create');
Route::get('payment-check', [\App\Http\Controllers\Api\CheckPaymentController::class, 'CheckPayment'])->name('payment-check');
Route::get('payment-response', [\App\Http\Controllers\Api\CheckPaymentController::class, 'response'])->name('payment-request.response');
