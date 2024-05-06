<?php

namespace App\Http\Controllers;

use App\Models\BatchOrderModel;
use App\Models\HistoryLogsModel;
use App\Models\ProductModel;
use App\Models\VoucherModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    public function example(Request $request)
    {

        // $vouchers = Vouchers::get();

        $coindesk = Http::get('https://api.coindesk.com/v1/bpi/currentprice.json');
        $news = Http::get('https://newsapi.org/v2/everything?domains=wsj.com&apiKey=af1c4f06fab94a0ab181ca1da3dfd9c6');

        $coindeskJSON = $coindesk->json();
        $newsJSON = $news->json();

        return response([
            'message' => "Current world update",
            'current_time' => Carbon::now(),
            'currency_values' => $coindeskJSON['bpi'],
            'world_news' => $newsJSON['articles']
        ], 200);
    }

    public function getAllVouchers()
    {
        $vouchers = VoucherModel::get();

        $vouchers = VoucherModel::query()
        ->leftJoin('product', 'voucher_main.product_id', '=', 'product.id')
        ->leftJoin('voucher_type', 'voucher_main.voucher_type_id', '=', 'voucher_type.id')
        ->select('voucher_main.*', 'product.product_name', 'voucher_type.voucher_name')
        ->get();

        return response([
            'message' => "All voucher displayed successfully",
            'return_code' => '0',
            'results' => $vouchers,
        ], 200);
    }



    public function getVoucher($serial)
    {
        $voucher = VoucherModel::where('serial', $serial)
            ->first();

        if (!$voucher) {
            return response([
                'message' => "Voucher not found",
                'return_code' => '-201',
            ], 404);
        }

        return response([
            'message' => "All voucher displayed successfully",
            'return_code' => '0',
            'results' => $voucher,
        ], 200);
    }

    public function nextAvailable($product_id)
    {
        $product = ProductModel::where('id', $product_id)->first();

        if (!$product) {
            return response([
                'message' => "The product ID you entered is not valid.",
                'return_code' => '-101',
            ], 404);
        }

        $voucher = VoucherModel::where('id', $product_id)
            ->where('available', true)
            ->where('deplete_date', null)
            ->first();

        if (!$voucher) {
            return response([
                'message' => "No available vouchers found",
                'return_code' => '-210',
            ], 200);
        }

        return response([
            'message' => "Available voucher found",
            'return_code' => '0',
            'results' => $voucher,
        ], 200);
    }

    public function createVoucher(Request $request, ErrorCodesController $errorCodesController)
    {
        $validator = Validator::make($request->all(), [
            'serial' => 'required|string|unique:voucher_main,serial',
            'product_id' => 'required|exists:product,id',
            'voucher_type_id' => 'required|exists:voucher_type,id',

            'SIM' => 'nullable|string',
            'PUK' => 'required|unique:voucher_main,PUK',
            'IMSI' => 'nullable|string',
            'MSISDN' => 'nullable|string',
            
            'service_reference' => 'nullable|string',
            'business_unit' => 'nullable|string',

            'batch_id' => 'required|exists:batch_order,batch_id',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            // Map validation errors to custom codes
            $customErrorCodes = $errorCodesController->mapValidationErrorsToCustomCodes($validator);

            // Fetch custom error messages from the database
            $errorMessages = $errorCodesController->getErrorMessagesFromCodes($customErrorCodes);

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $errorMessages,
            ], 422);
        }

        $voucher = VoucherModel::create([
            'serial' => $request->serial,
        
            'product_id' => $request->product_id,
            'voucher_type_id' => $request->voucher_type_id,
        
            'SIM' => $request->SIMNo,
            'PUK' => $request->PUK,
            'IMSI' => $request->IMSI,
            'MSISDN' => $request->MSISDN,
            
            'service_reference' => $request->service_reference,
            'business_unit' => $request->business_unit,
            
            'batch_id' => $request->batch_id,
            'note' => $request->note,
            'created_by' => $request->attributes->get('preferred_username'),
        ]);

        $batchOrder = BatchOrderModel::where('batch_id', $request->batch_id)->first();
        $batchOrder->batch_count = $batchOrder->batch_count + 1;
        $batchOrder->save();

        $history = new HistoryLogsModel();
        $history->username = $request->attributes->get('preferred_username');
        $history->transaction = "Created Voucher";
        $history->database_table = "voucher_main";
        $history->new_data = json_encode($voucher);
        $history->save();

        return response([
            'message' => "Voucher created successfully",
            'return_code' => '0',
            'results' => $voucher
        ], 201);
    }

    public function editVoucher($serial, Request $request)
    {
        $voucher = VoucherModel::where('serial', $serial)->first();

        if (!$voucher) {
            return response([
                'message' => "Voucher not found",
                'return_code' => '-201',
            ], 404);
        }

        $request->validate([
            // 'expire_date' => 'nullable|date_format:Y-m-d|after:today',
            // 'value' => 'nullable|integer',
            // 'serial' => 'required|string|unique:voucher_main,serial',

            // 'product_code' => 'required|exists:product,product_code',
            'product_id' => 'required|exists:product,id',

            // 'IMEI' => 'nullable|string',
            // 'SIMNarrative' => 'nullable|string',
            // 'PCN' => 'nullable|string',
            'SIMNo' => 'nullable|string',
            'PUK' => 'nullable|unique:voucher_main,PUK,'.$voucher->id,
            'IMSI' => 'nullable|string',
            'MSISDN' => 'nullable|string',
            
            'service_reference' => 'nullable|string',
            'business_unit' => 'nullable|string',
            
            // 'batch_id' => 'required|exists:batch_order,batch_id',
            'note' =>  'nullable|string',
        ]);

        $voucher_old = clone $voucher;

        $productCode = ProductModel::where('product_id', $request->product_id)->first();

        $voucher->update([
            // 'expire_date' => $request->expire_date,
            // 'value' => $request->value,
            // 'serial' => $request->serial,
        
            // 'product_code' => $productCode->product_code,
            'product_id' => $request->product_id,
        
            // 'IMEI' => $request->IMEI,
            // 'SIMNarrative' => $request->SIMNarrative,
            // 'PCN' => $request->PCN,
            'SIM' => $request->SIMNo,
            'PUK' => $request->PUK,
            'IMSI' => $request->IMSI,
            'MSISDN' => $request->MSISDN,
            
            'service_reference' => $request->service_reference,
            'business_unit' => $request->business_unit,
            
            // 'batch_id' => $request->batch_id,
            'note' => $request->note,
            'updated_by' => $request->attributes->get('preferred_username'),
        ]);

        $voucher->refresh();

        if ($voucher->wasChanged()) { 
            $history = new HistoryLogsModel();
            $history->username = $request->attributes->get('preferred_username');
            $history->transaction = "Edited Voucher";
            $history->database_table = "voucher_main";
            $history->old_data = json_encode($voucher_old->toArray());
            $history->new_data = json_encode($voucher->toArray());
            $history->save();
        }

        return response([
            'message' => "Voucher updated successfully",
            'return_code' => '0',
            'results' => $voucher,
        ], 200);
    }

    
    public function setVoucherActive($serial, Request $request)
    {
        $voucher = VoucherModel::where('serial', $serial)->first();
        $voucher_old = VoucherModel::where('serial', $serial)->get();

        if (!$voucher) {
            return response([
                'message' => "Voucher not found",
                'return_code' => '-201',
            ], 404);
        }

        if ($voucher->deplete_date != null) {
            return response([
                'message' => "Can not activate a depleted voucher",
                'return_code' => '-204',
            ], 404);
        }

        if ($voucher->available == true) {
            return response([
                'message' => "Voucher is already active",
                'return_code' => '-207',
            ], 404);
        }

        $voucher->available = true;
        $voucher->save();
        $voucher_new = array(json_decode($voucher, true));

        $history = new HistoryLogsModel();
        $history->username = $request->attributes->get('preferred_username');
        $history->transaction = "Voucher set as active";
        $history->database_table = "voucher_main";
        $history->old_data = json_encode($voucher_old);
        $history->new_data = json_encode($voucher_new);
        $history->save();

        return response([
            'message' => "Voucher set as active",
            'return_code' => '0',
            'results' => $voucher
        ], 201);
    }

    public function setVoucherInactive($serial, Request $request)
    {
        $voucher = VoucherModel::where('serial', $serial)->first();
        $voucher_old = VoucherModel::where('serial', $serial)->get();

        if (!$voucher) {
            return response([
                'message' => "Voucher not found",
                'return_code' => '-201',
            ], 404);
        }

        if ($voucher->available == false) {
            return response([
                'message' => "Voucher is already inactive",
                'return_code' => '-207',
            ], 404);
        }

        $voucher->available = false;
        $voucher->save();
        $voucher_new = array(json_decode($voucher, true));

        $history = new HistoryLogsModel();
        $history->username = $request->attributes->get('preferred_username');
        $history->transaction = "Voucher set as inactive";
        $history->database_table = "voucher_main";
        $history->old_data = json_encode($voucher_old);
        $history->new_data = json_encode($voucher_new);
        $history->save();

        return response([
            'message' => "Voucher set as inactive",
            'return_code' => '0',
            'results' => $voucher
        ], 201);
    }

    public function massVoucherStatusInactive(Request $request)
    {
        $voucher_code = $request->input('voucher_code');
        $available = false;

        $vouchers = VoucherModel::whereIn('voucher_code', $voucher_code)->get();
        $vouchers_old = VoucherModel::whereIn('voucher_code', $voucher_code)->get();

        if ($vouchers->isEmpty()) {
            return response()->json([
                'message' => 'Vouchers not found',
                'return_code' => '-201',
            ], 404);
        }

        foreach ($vouchers as $voucher) {
            $voucher->available = $available;
            $voucher->save();
        }
        $vouchers_new = $vouchers;

        $history = new HistoryLogsModel();
        $history->username = $request->attributes->get('preferred_username');
        $history->transaction = "Batch deactivated vouchers";
        $history->database_table = "voucher_main";
        $history->old_data = json_encode($vouchers_old);
        $history->new_data = json_encode($vouchers_new);
        $history->save();

        return response()->json([
            'message' => 'Voucher(s) updated successfully',
            'return_code' => '0',
            'results' => $vouchers
        ], 201);
    }
}