<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BatchOrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SoapVoucherController;
use App\Http\Controllers\VoucherActivationController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\VoucherHistoryController;
use App\Http\Controllers\VoucherMainController;
use App\Http\Controllers\WebServiceController;
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
});


Route::group(['middleware' => 'token-validation'], function () {

    // Voucher API
    Route::get('/getAllVouchers', [VoucherController::class, 'getAllVouchers']);
    Route::get('/getVoucher/{serial}', [VoucherController::class, 'getVoucher']);
    Route::get('/nextAvailable/{product_id}', [VoucherController::class, 'nextAvailable']);
    Route::post('/createVoucher', [VoucherController::class, 'createVoucher']);
    // Route::post('/createVoucherCSV', [VoucherController::class, 'createVoucherCSV']);
    // Route::post('/voucher-file', [VoucherController::class, 'createNewVoucherCSV']);
    Route::put('/editVoucher/{serial}', [VoucherController::class, 'editVoucher']);
    Route::patch('/setActive/{serial}', [VoucherController::class, 'setVoucherActive']);
    Route::patch('/setInactive/{serial}', [VoucherController::class, 'setVoucherInactive']);

    //Voucher Activation API
    Route::post('/consumeVoucher', [VoucherActivationController::class, 'consumeVoucher']);


    //Voucher History API
    Route::get('/voucher-history', [VoucherHistoryController::class, 'getAllHistory']);
    Route::get('/product-history', [VoucherHistoryController::class, 'getProductHistory']);
    Route::get('/batch-order-history', [VoucherHistoryController::class, 'getBatchOrderHistory']);

    //Product API
    Route::get('/product', [ProductController::class, 'getAllProducts']);
    Route::post('/product', [ProductController::class, 'createNewProduct']);
    Route::get('/product/{id}', [ProductController::class, 'getProductByID']);
    Route::put('/product/{id}', [ProductController::class, 'editProductByID']);
    Route::delete('/product/{id}', [ProductController::class, 'deleteProductByID']);

    //Batch order API
    Route::get('/batchOrder', [BatchOrderController::class, 'getAllBatchOrder']);
    Route::post('/batchOrder', [BatchOrderController::class, 'createBatchOrder']);

    Route::post('/testReq', [BatchOrderController::class, 'testReq']);

    Route::get('/batchOrder/{batch_id}', [BatchOrderController::class, 'getBatchOrderByVoucherID']);
    Route::put('/batchOrder/{batch_id}', [BatchOrderController::class, 'editBatchOrderByID']);
    Route::delete('/batchOrder/{batch_id}', [BatchOrderController::class, 'deleteBatchOrderByID']);

    //Web Service API
    Route::get('/service', [WebServiceController::class, 'getAllApplication']);
    Route::get('/service/{id}', [WebServiceController::class, 'getApplicationByID']);
    Route::post('/service', [WebServiceController::class, 'submitApplication']);

});


// -------------Inactive routes--------------
//OLD Voucher API
// Route::get('/example', [VoucherController::class, 'example']);
// Route::get('/voucher', [VoucherController::class, 'getAllVouchers']);
// Route::get('/voucher/{voucherCode}', [VoucherController::class, 'getVoucherByCode']);
// Route::post('/voucher', [VoucherController::class, 'createNewVoucherMultple']);
// Route::post('/voucher-array', [VoucherController::class, 'createNewVoucherArray']);
// Route::post('/voucher-file', [VoucherController::class, 'createNewVoucherCSV']);
// Route::put('/voucher/{voucherCode}', [VoucherController::class, 'editVoucherByCode']);
// Route::patch('/voucher-set-active/{voucherCode}', [VoucherController::class, 'setVoucherActive']);
// Route::patch('/voucher-set-inactive/{voucherCode}', [VoucherController::class, 'setVoucherInactive']);
// Route::post('/voucher-mass-active', [VoucherController::class, 'massVoucherStatusActive']);
// Route::post('/voucher-mass-inactive', [VoucherController::class, 'massVoucherStatusInactive']);

//Voucher SOAP API
// Route::get('/voucher-soap', [SoapVoucherController::class, 'SOAPGetAllVouchers']);
// Route::get('/voucher-soap/{voucherCode}', [SoapVoucherController::class, 'SOAPGetVoucherByCode']);
// Route::post('/voucher-soap', [SoapVoucherController::class, 'SOAPCreateNewVoucher']);
// Route::put('/voucher-soap/{voucherCode}', [SoapVoucherController::class, 'SOAPEditVoucherByCode']);

//Authentication API
// Route::post('/register', [AuthenticationController::class, 'register']);
// Route::post('/login', [AuthenticationController::class, 'login']);