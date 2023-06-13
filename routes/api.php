<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\SoapVoucherController;
use App\Http\Controllers\VoucherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    //Authentication API
    Route::get('/user', [AuthenticationController::class, 'getCurrentUser']);
    Route::post('/logout', [AuthenticationController::class, 'logout']);

    //Voucher API
    Route::get('/example', [VoucherController::class, 'example']);
    Route::get('/voucher', [VoucherController::class, 'getAllVouchers']);
    Route::get('/voucher/{voucherCode}', [VoucherController::class, 'getVoucherByCode']);
    Route::post('/voucher', [VoucherController::class, 'createNewVoucher']);
    Route::delete('/voucher/{voucherCode}', [VoucherController::class, 'setVoucherInactive']);

    //Voucher SOAP API
    Route::get('/voucher-soap', [SoapVoucherController::class, 'SOAPGetAllVouchers']);
    Route::get('/voucher-soap/{voucherCode}', [SoapVoucherController::class, 'SOAPGetVoucherByCode']);
    Route::post('/voucher-soap', [SoapVoucherController::class, 'SOAPCreateNewVoucher']);

});

//Authentication API
Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('/login', [AuthenticationController::class, 'login']);