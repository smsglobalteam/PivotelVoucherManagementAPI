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
    Route::get('/example', [VoucherController::class, 'example'])->middleware('role:PVMS-viewer,PVMS-management,PVMS-upload');

    //Token Validation API
    Route::get('/tokeninspect', [TokenController::class, 'tokeninspect']);

    // Voucher API
    Route::get('/getAllVouchers', [VoucherController::class, 'getAllVouchers'])->middleware('role:PVMS-viewer,PVMS-management,PVMS-upload');
    Route::get('/getVoucher/{serial}', [VoucherController::class, 'getVoucher'])->middleware('role:PVMS-viewer,PVMS-management');
    Route::get('/nextAvailable/{product_id}', [VoucherController::class, 'nextAvailable'])->middleware('role:PVMS-viewer,PVMS-management');
    Route::post('/createVoucher', [VoucherController::class, 'createVoucher'])->middleware('role:PVMS-management');
    Route::put('/editVoucher/{serial}', [VoucherController::class, 'editVoucher'])->middleware('role:PVMS-management');
    Route::patch('/setActive/{serial}', [VoucherController::class, 'setVoucherActive'])->middleware('role:PVMS-management');
    Route::patch('/setInactive/{serial}', [VoucherController::class, 'setVoucherInactive'])->middleware('role:PVMS-management');

    //Voucher Type API
    Route::get('/voucherType', [VoucherTypeController::class, 'getAllVoucherType'])->middleware('role:PVMS-viewer,PVMS-management');
    Route::post('/voucherType', [VoucherTypeController::class, 'createNewVoucherType'])->middleware('role:PVMS-management');
    Route::get('/voucherType/{id}', [VoucherTypeController::class, 'getAllVoucherByID'])->middleware('role:PVMS-viewer,PVMS-management');
    Route::put('/voucherType/{id}', [VoucherTypeController::class, 'editVoucherTypeByCode'])->middleware('role:PVMS-management');

    //Voucher Activation API
    Route::post('/consumeVoucher', [VoucherActivationController::class, 'consumeVoucher'])->middleware('role:PVMS-management');

    //Voucher History API
    Route::get('/voucher-history', [HistoryLogsController::class, 'getAllHistory'])->middleware('role:PVMS-viewer,PVMS-management');
    Route::get('/voucher-histor{database_table}', [HistoryLogsController::class, 'getHistoryLogsByTable'])->middleware('role:PVMS-viewer,PVMS-management');

    //Product API
    Route::get('/product', [ProductController::class, 'getAllProducts'])->middleware('role:PVMS-viewer,PVMS-management');
    Route::post('/product', [ProductController::class, 'createNewProduct'])->middleware('role:PVMS-management');
    Route::get('/product/{id}', [ProductController::class, 'getProductByID'])->middleware('role:PVMS-viewer,PVMS-management');
    Route::put('/product/{id}', [ProductController::class, 'editProductByID'])->middleware('role:PVMS-management');
    Route::delete('/product/{id}', [ProductController::class, 'deleteProductByID'])->middleware('role:PVMS-management');

    //Error Codes API
    Route::get('/errorCodes', [ErrorCodesController::class, 'getAllErorrCodes'])->middleware('role:PVMS-viewer,PVMS-management');
    Route::post('/errorCodes', [ErrorCodesController::class, 'createNewErrorCode'])->middleware('role:PVMS-management');
    Route::get('/errorCodes/{id}', [ErrorCodesController::class, 'getErrorCodeByID'])->middleware('role:PVMS-viewer,PVMS-management');
    Route::put('/errorCodes/{id}', [ErrorCodesController::class, 'editErrorByCode'])->middleware('role:PVMS-management');
    Route::delete('/errorCodes/{id}', [ErrorCodesController::class, 'deleteErrorCodeByID'])->middleware('role:PVMS-management');
    Route::post('/errorMessages', [ErrorCodesController::class, 'getErrorMessages'])->middleware('role:PVMS-management');

    //Batch order API
    Route::get('/batchOrder', [BatchOrderController::class, 'getAllBatchOrder'])->middleware('role:PVMS-viewer,PVMS-upload,PVMS-management');
    Route::post('/batchOrder', [BatchOrderController::class, 'createBatchOrder'])->middleware('role:PVMS-upload,PVMS-management');
    Route::get('/batchOrder/{batch_id}', [BatchOrderController::class, 'getBatchOrderByVoucherID'])->middleware('role:PVMS-viewer,PVMS-upload,PVMS-management');
    Route::put('/batchOrder/{batch_id}', [BatchOrderController::class, 'editBatchOrderByID'])->middleware('role:PVMS-upload,PVMS-management');
    Route::delete('/batchOrder/{batch_id}', [BatchOrderController::class, 'deleteBatchOrderByID'])->middleware('role:PVMS-management');

    //File testing
    Route::post('/testReq', [BatchOrderController::class, 'testReq'])->middleware('role:PVMS-management');

    //Web Service API
    Route::get('/service', [WebServiceController::class, 'getAllApplication'])->middleware('role:PVMS-viewer,PVMS-management');
    Route::get('/service/{id}', [WebServiceController::class, 'getApplicationByID'])->middleware('role:PVMS-viewer,PVMS-management');
    Route::post('/service', [WebServiceController::class, 'submitApplication'])->middleware('role:PVMS-management');

    //Web Service Plans API
    Route::get('/servicePlans', [WebServicePlansController::class, 'getAllServicePlans'])->middleware('role:PVMS-viewer,PVMS-management');
    Route::post('/servicePlans', [WebServicePlansController::class, 'createNewServicePlan'])->middleware('role:PVMS-management');
    Route::get('/servicePlans/{id}', [WebServicePlansController::class, 'getAllServicePlansByCode'])->middleware('role:PVMS-viewer,PVMS-management');
    Route::put('/servicePlans/{code}', [WebServicePlansController::class, 'editServicePlanByCode'])->middleware('role:PVMS-management');
});
