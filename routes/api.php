<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BankCardController;
use App\Http\Controllers\ResultBeconController;
use App\Http\Controllers\ResultEmerdController;
use App\Http\Controllers\ResultSpareController;
use App\Http\Controllers\ResultParityController;
use App\Http\Controllers\TotalBalanceController;
use App\Http\Controllers\CurrentBalanceController;
use App\Http\Controllers\PaymentRequestController;
use App\Http\Controllers\WithdrawRequestController;
use App\Http\Controllers\MybettingRecordsController;

/* Public Routes */

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'store']);
Route::post('/ForgetPassword', [UserController::class, 'forgetPassword']);
Route::post('/ResetPaasword', [UserController::class, 'UpdatePassword']);
Route::post('/getPass', [UserController::class, 'getOtp']);
Route::get('/calcPeriod', [MybettingRecordsController::class, 'calcPeriod']);

/* End Public Routes */



/* Protected Routes */
// Route::post('/bet',[MybettingRecordsController::class,'store']);
Route::get('/bet/{category}/{period}', [MybettingRecordsController::class, 'smallestResult']);


// Route::post('/Request',[PaymentRequestController::class,'PaymentRequest']);


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);

    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    Route::get('/logout', [UserController::class, 'logout']);
    Route::post('/bet', [MybettingRecordsController::class, 'store']);
    // Route::get('/resultP/{period}', [ResultParityController::class, 'show']); Not in use Yet
    Route::get('/resultsP/{period}', [ResultParityController::class, 'index']);
    Route::post('/ManualResult', [ResultParityController::class, 'manualResult']);
    Route::get('/resultsS/{period}', [ResultSpareController::class, 'index']);
    Route::get('/resultsB/{period}', [ResultBeconController::class, 'index']);
    Route::get('/resultsE/{period}', [ResultEmerdController::class, 'index']);
    Route::post('/payment', [PaymentController::class, 'payumoney']);
    Route::post('/Request', [PaymentRequestController::class, 'PaymentRequest']);
    Route::post('/AddCard', [BankCardController::class, 'Store']);
    Route::get('/UserCards', [BankCardController::class, 'index']);
    Route::get('/AllRequest', [PaymentRequestController::class, 'index']);
    Route::get('/showRequest/{days}', [PaymentRequestController::class, 'showRequest']);
    Route::get('/changeStatus/{UTR}', [PaymentRequestController::class, 'changeStatus']);
    Route::get('/GetBalance/{Period}', [CurrentBalanceController::class, 'index']);
    Route::get('/calcBalance', [TotalBalanceController::class, 'calcTotalAmount']);
    Route::get('/earnings/{from?}/{to?}', [TotalBalanceController::class, 'earningCalculation']);
    Route::post('/WithdrawRequest', [WithdrawRequestController::class, 'addWithRequest']);
    Route::get('/AllWithdrawRequest', [WithdrawRequestController::class, 'index']);
    Route::get('/ApproveWithdraw/{id}', [WithdrawRequestController::class, 'approveWithdraw']);
    Route::get('/RejectWithdraw/{id}', [WithdrawRequestController::class, 'rejectWithdraw']);
    Route::post('/changeUpi', [UserController::class, 'changeUpi']);
    Route::get('/fetchUpi', [UserController::class, 'fetchUpi']);
    Route::get('/History', [MybettingRecordsController::class, 'usersBet']);
});

/* End Protected Routes */
