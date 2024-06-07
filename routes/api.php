<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BatchOrderController;
use App\Http\Controllers\ErrorCodesController;
use App\Http\Controllers\HistoryLogsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SoapVoucherController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\VoucherActivationController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\VoucherHistoryController;
use App\Http\Controllers\VoucherMainController;
use App\Http\Controllers\VoucherTypeController;
use App\Http\Controllers\WebServiceController;
use App\Http\Controllers\WebServicePlansController;
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
    //Example API
    Route::get('/example', [VoucherController::class, 'example']);

    //Token Validation API
    Route::get('/tokeninspect', [TokenController::class, 'tokeninspect']);

    // Voucher API
    Route::get('/getAllVouchers', [VoucherController::class, 'getAllVouchers']);
    Route::get('/getVoucher/{serial}', [VoucherController::class, 'getVoucher']);
    Route::get('/nextAvailable/{product_id}', [VoucherController::class, 'nextAvailable']);
    Route::post('/createVoucher', [VoucherController::class, 'createVoucher']);
    Route::put('/editVoucher/{serial}', [VoucherController::class, 'editVoucher']);
    Route::patch('/setActive/{serial}', [VoucherController::class, 'setVoucherActive']);
    Route::patch('/setInactive/{serial}', [VoucherController::class, 'setVoucherInactive']);

    //Voucher Type API
    Route::get('/voucherType', [VoucherTypeController::class, 'getAllVoucherType']);
    Route::post('/voucherType', [VoucherTypeController::class, 'createNewVoucherType']);
    Route::get('/voucherType/{id}', [VoucherTypeController::class, 'getAllVoucherByID']);
    Route::put('/voucherType/{id}', [VoucherTypeController::class, 'editVoucherTypeByCode']);

    //Voucher Activation API
    Route::post('/consumeVoucher', [VoucherActivationController::class, 'consumeVoucher']);

    //Voucher History API
    Route::get('/voucher-history', [HistoryLogsController::class, 'getAllHistory']);
    Route::get('/voucher-histor{database_table}', [HistoryLogsController::class, 'getHistoryLogsByTable']);

    //Product API
    Route::get('/product', [ProductController::class, 'getAllProducts']);
    Route::post('/product', [ProductController::class, 'createNewProduct']);
    Route::get('/product/{id}', [ProductController::class, 'getProductByID']);
    Route::put('/product/{id}', [ProductController::class, 'editProductByID']);
    Route::delete('/product/{id}', [ProductController::class, 'deleteProductByID']);

    //Error Codes API
    Route::get('/errorCodes', [ErrorCodesController::class, 'getAllErorrCodes']);
    Route::post('/errorCodes', [ErrorCodesController::class, 'createNewErrorCode']);
    Route::get('/errorCodes/{id}', [ErrorCodesController::class, 'getErrorCodeByID']);
    Route::put('/errorCodes/{id}', [ErrorCodesController::class, 'editErrorByCode']);
    Route::delete('/errorCodes/{id}', [ErrorCodesController::class, 'deleteErrorCodeByID']);
    Route::post('/errorMessages', [ErrorCodesController::class, 'getErrorMessages']);

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

    //Web Service Plans API
    Route::get('/servicePlans', [WebServicePlansController::class, 'getAllServicePlans']);
    Route::post('/servicePlans', [WebServicePlansController::class, 'createNewServicePlan']);
    Route::get('/servicePlans/{id}', [WebServicePlansController::class, 'getAllServicePlansByCode']);
    Route::put('/servicePlans/{code}', [WebServicePlansController::class, 'editServicePlanByCode']);
});
