<?php

namespace App\Http\Controllers;

use App\Models\VoucherHistory;
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
        $voucherHistory = VoucherModel::get();

        return response([
            'message' => "All voucher displayed successfully",
            'return_code' => '0',
            'results' => $voucherHistory,
        ], 200);
    }



    public function getVoucher($voucherCode)
    {
        $voucher = VoucherModel::where('voucher_code', $voucherCode)
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

    public function createVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|unique:voucher_main,voucher_code',
            'product_code_reference' => 'nullable|exists:product,product_code',
            'expiry_date' => 'nullable|date_format:Y-m-d|after:today',

            'value' => 'required|integer',

            'serviceID' => 'required|string',
            'business_unit' => 'required|string',
            'serial_number' => 'required|string|unique:voucher_main,serial_number',

            'IMEI' => 'required|string',
            'SIMNarrative' => 'required|string',
            'SIMNo' => 'required|string',
            'IMSI' => 'required|string',
            'PUK' => 'required|string',
        ]);

        $voucher = VoucherModel::create([
            'voucher_code' => $request->voucher_code,
            'product_code_reference' => $request->product_code_reference,
            'expiry_date' => $request->expiry_date,

            'value' => $request->value,

            'serviceID' => $request->serviceID,
            'business_unit' => $request->business_unit,
            'serial_number' => $request->serial_number,

            'IMEI' => $request->IMEI,
            'SIMNarrative' => $request->SIMNarrative,
            'SIMNo' => $request->SIMNo,
            'IMSI' => $request->IMSI,
            'PUK' => $request->PUK,

            'created_by' => 1
        ]);

        $history = new VoucherHistory();
        $history->user_id = 1;
        $history->transaction = "Created voucher";
        $history->voucher_new_data = json_encode($voucher);
        $history->save();

        return response([
            'message' => "Voucher created successfully",
            'return_code' => '0',
            'results' => $voucher
        ], 201);
    }

    public function createVoucherCSV(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');

        // Check the file extension
        $extension = $file->getClientOriginalExtension();
        if ($extension !== 'csv') {
            return response([
                'message' => 'Invalid file format. Only CSV files are supported.',
                'return_code' => '-205',
            ], 422);
        }

        $filePath = $file->getPathname();
        $file = fopen($filePath, 'r');

        // Skip the header row
        fgetcsv($file);

        $vouchers = [];

        while (($row = fgetcsv($file)) !== false) {
            $voucherCode = $row[0]; // Assuming the first column is 'voucher_code'
            $productCodeReference = $row[1];
            $expiryDate = $row[2];
            $value = $row[3];
            $serviceID = $row[4];
            $businessUnit = $row[5];
            $serialNumber = $row[6];
            $IMEI = $row[7];
            $SIMNarrative = $row[8];
            $SIMNo = $row[9];
            $IMSI = $row[10];
            $PUK = $row[11];

            // Validate the data
            $validator = Validator::make([
                'voucher_code' => $voucherCode,
                'product_code_reference' => $productCodeReference,
                'expiry_date' => $expiryDate,
                'value' => $value,
                'serviceID' => $serviceID,
                'business_unit' => $businessUnit,
                'serial_number' => $serialNumber,
                'IMEI' => $IMEI,
                'SIMNarrative' => $SIMNarrative,
                'SIMNo' => $SIMNo,
                'IMSI' => $IMSI,
                'PUK' => $PUK,
            ], [
                'voucher_code' => 'required|unique:voucher_main,voucher_code',
                'product_code_reference' => 'nullable|exists:product,product_code',
                'expiry_date' => 'nullable|date_format:Y-m-d|after:today',
                'value' => 'required|integer',
                'serviceID' => 'required|string',
                'business_unit' => 'required|string',
                'serial_number' => 'required|string|unique:voucher_main,serial_number',
                'IMEI' => 'required|string',
                'SIMNarrative' => 'required|string',
                'SIMNo' => 'required|string',
                'IMSI' => 'required|string',
                'PUK' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response([
                    'message' => 'Invalid data in the file.',
                    'return_code' => '-206',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $voucher = VoucherModel::create([
                'voucher_code' => $voucherCode,
                'product_code_reference' => $productCodeReference,
                'expiry_date' => $expiryDate,
                'value' => $value,
                'serviceID' => $serviceID,
                'business_unit' => $businessUnit,
                'serial_number' => $serialNumber,
                'IMEI' => $IMEI,
                'SIMNarrative' => $SIMNarrative,
                'SIMNo' => $SIMNo,
                'IMSI' => $IMSI,
                'PUK' => $PUK,
                'created_by' => 1, // Assuming 'created_by' is still required. Adjust as necessary.
            ]);

            $vouchers[] = $voucher;
        }

        fclose($file);

        $history = new VoucherHistory();
        $history->user_id = 1;
        $history->transaction = "File created vouchers";
        $history->voucher_new_data = json_encode($vouchers);
        $history->save();

        return response([
            'message' => 'Vouchers created successfully',
            'return_code' => '0',
            'results' => $vouchers,
        ], 201);
    }


    public function editVoucher($voucherCode, Request $request)
    {
        $voucher = VoucherModel::where('voucher_code', $voucherCode)->first();

        if (!$voucher) {
            return response([
                'message' => "Voucher not found",
                'return_code' => '-201',
            ], 404);
        }

        $request->validate([
            'product_code_reference' => 'nullable|exists:product,product_code',
            'expiry_date' => 'nullable|date_format:Y-m-d|after:today',
            'value' => 'required|integer',
            'serviceID' => 'required|string',
            'business_unit' => 'required|string',
            'serial_number' => 'required|string|unique:voucher_main,serial_number,' . $voucher->id,
            'IMEI' => 'required|string',
            'SIMNarrative' => 'required|string',
            'SIMNo' => 'required|string',
            'IMSI' => 'required|string',
            'PUK' => 'required|string',
        ]);

        $voucher_old = clone $voucher;

        $voucher->update([
            'product_code_reference' => $request->product_code_reference,
            'expiry_date' => $request->expiry_date,
            'value' => $request->value,
            'serviceID' => $request->serviceID,
            'business_unit' => $request->business_unit,
            'serial_number' => $request->serial_number,
            'IMEI' => $request->IMEI,
            'SIMNarrative' => $request->SIMNarrative,
            'SIMNo' => $request->SIMNo,
            'IMSI' => $request->IMSI,
            'PUK' => $request->PUK,
            'created_by' => 1 
        ]);

        $voucher->refresh();

        if ($voucher->wasChanged()) { 
            $history = new VoucherHistory();
            $history->user_id = 1;
            $history->transaction = "Edited voucher";
            $history->voucher_old_data = json_encode($voucher_old->toArray());
            $history->voucher_new_data = json_encode($voucher->toArray());
            $history->save();
        }

        return response([
            'message' => "Voucher updated successfully",
            'return_code' => '0',
            'results' => $voucher,
        ], 200);
    }

    
    public function setVoucherActive($voucherCode)
    {
        $voucher = VoucherModel::where('voucher_code', $voucherCode)->first();
        $voucher_old = VoucherModel::where('voucher_code', $voucherCode)->get();

        if (!$voucher) {
            return response([
                'message' => "Voucher not found",
                'return_code' => '-201',
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

        $history = new VoucherHistory();
        $history->user_id = 1;
        $history->transaction = "Activated voucher";
        $history->voucher_old_data = json_encode($voucher_old);
        $history->voucher_new_data = json_encode($voucher_new);
        $history->save();

        return response([
            'message' => "Voucher set as active",
            'return_code' => '0',
            'results' => $voucher
        ], 201);
    }

    public function setVoucherInactive($voucherCode)
    {
        $voucher = VoucherModel::where('voucher_code', $voucherCode)->first();
        $voucher_old = VoucherModel::where('voucher_code', $voucherCode)->get();

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

        $history = new VoucherHistory();
        $history->user_id = 1;
        $history->transaction = "Activated voucher";
        $history->voucher_old_data = json_encode($voucher_old);
        $history->voucher_new_data = json_encode($voucher_new);
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

        $history = new VoucherHistory();
        $history->user_id = 1;
        $history->transaction = "Batch deactivated vouchers";
        $history->voucher_old_data = json_encode($vouchers_old);
        $history->voucher_new_data = json_encode($vouchers_new);
        $history->save();

        return response()->json([
            'message' => 'Voucher(s) updated successfully',
            'return_code' => '0',
            'results' => $vouchers
        ], 201);
    }
}