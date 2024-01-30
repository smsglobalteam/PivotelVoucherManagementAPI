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
        $vouchers = DB::table('voucher')
            ->select(
                'voucher.*',
                // Select all columns from the voucher table
                'product.product_code',
                'product.product_type',
                'product.product_name'

            )
            ->leftJoin('product', 'voucher.product_code_reference', '=', 'product.product_code')
            ->get();

        return response([
            'message' => "All vouchers displayed successfully",
            'results' => $vouchers
        ], 200);
    }

    public function getVoucherByCode($voucherCode)
    {
        $voucher = VoucherModel::where('voucher_code', $voucherCode)
            ->select(
                'voucher.*',
                // Select all columns from the voucher table
                'product.product_code',
                'product.product_type',
                'product.product_name'

            )
            ->leftJoin('product', 'voucher.product_code_reference', '=', 'product.product_code')
            ->get();

        if (!$voucher) {
            return response([
                'message' => "Voucher not found",
            ], 404);
        }

        return response([
            'message' => "Voucher displayed successfully",
            'results' => $voucher
        ], 200);
    }

    public function createNewVoucherMultple(Request $request)
    {
        $request->validate([
            'serial' => 'nullable',
            'product_code_reference' => 'nullable|exists:product,product_code',
            'value' => 'required|integer',
            'expiry_date' => 'nullable|date_format:Y-m-d',
            'service_reference' => 'nullable|integer',
            'voucher_count' => 'required|integer|min:1',
            'IMEI' => 'nullable',
            'PCN' => 'nullable',
            'sim_number' => 'nullable',
            'IMSI' => 'nullable',
            'PUK' => 'nullable',
        ]);

        function generateVoucherCode($length)
        {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $code = '';
            $max = strlen($characters) - 1;

            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, $max)];
            }

            return $code;
        }

        $vouchers = [];
        $count = $request->voucher_count;

        // Generate unique voucher codes
        for ($i = 0; $i < $count; $i++) {
            $voucherCodeExists = true;
            $voucherCode = '';

            while ($voucherCodeExists) {
                $voucherCode = generateVoucherCode(16);
                $voucherCodeExists = VoucherModel::where('voucher_code', $voucherCode)->exists();
            }

            $voucher = VoucherModel::create([
                'serial' => $request->serial,
                'product_code_reference' => $request->product_code_reference,
                'voucher_code' => $voucherCode,
                'value' => $request->value,
                'expiry_date' => $request->expiry_date,
                'available' => true,
                'service_reference' => $request->service_reference,
                'IMEI' => $request->IMEI,
                'PCN' => $request->PCN,
                'sim_number' => $request->sim_number,
                'IMSI' => $request->IMSI,
                'PUK' => $request->PUK,
                'created_by' => 1
            ]);

            $vouchers[] = $voucher;
        }

        $history = new VoucherHistory();
        $history->user_id = 1;
        $history->transaction = "Batch created vouchers";
        $history->voucher_new_data = json_encode($vouchers);
        $history->save();

        return response([
            'message' => "Vouchers created successfully",
            'results' => $vouchers
        ], 200);
    }

    public function createNewVoucherArray(Request $request)
    {
        $request->validate([
            '*.serial' => 'nullable',
            '*.product_code_reference' => 'nullable|exists:product,product_code',
            '*.value' => 'required|integer',
            '*.expiry_date' => 'nullable|date_format:Y-m-d',
            '*.service_reference' => 'nullable|integer',
            '*.voucher_count' => 'required|integer|min:1',
            '*.IMEI' => 'nullable',
            '*.PCN' => 'nullable',
            '*.sim_number' => 'nullable',
            '*.IMSI' => 'nullable',
            '*.PUK' => 'nullable',
        ]);

        function generateVoucherCode($length)
        {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $code = '';
            $max = strlen($characters) - 1;

            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, $max)];
            }

            return $code;
        }

        $vouchers = [];

        foreach ($request->all() as $entry) {
            $count = $entry['voucher_count'];

            // Generate unique voucher codes
            for ($i = 0; $i < $count; $i++) {
                $voucherCodeExists = true;
                $voucherCode = '';

                while ($voucherCodeExists) {
                    $voucherCode = generateVoucherCode(16);
                    $voucherCodeExists = VoucherModel::where('voucher_code', $voucherCode)->exists();
                }

                $voucher = VoucherModel::create([
                    'serial' => $entry['serial'],
                    'product_code_reference' => $entry['product_code_reference'],
                    'voucher_code' => $voucherCode,
                    'value' => $entry['value'],
                    'expiry_date' => $entry['expiry_date'],
                    'available' => true,
                    'service_reference' => $entry['service_reference'],
                    'IMEI' => $entry['IMEI'],
                    'PCN' => $entry['PCN'],
                    'sim_number' => $entry['sim_number'],
                    'IMSI' => $entry['IMSI'],
                    'PUK' => $entry['PUK'],
                    'created_by' => 1
                ]);

                $vouchers[] = $voucher;
            }
        }

        $history = new VoucherHistory();
        $history->user_id = 1;
        $history->transaction = "Batch created vouchers";
        $history->voucher_new_data = json_encode($vouchers);
        $history->save();

        return response([
            'message' => "Vouchers created successfully",
            'results' => $vouchers
        ], 200);
    }

    public function createNewVoucherCSV(Request $request)
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
            ], 400);
        }

        $filePath = $file->getPathname();
        $file = fopen($filePath, 'r');

        // Skip the header row
        fgetcsv($file);

        function generateVoucherCode($length)
        {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $code = '';
            $max = strlen($characters) - 1;

            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, $max)];
            }

            return $code;
        }

        $vouchers = [];

        while (($row = fgetcsv($file)) !== false) {
            $serial = $row[0];
            $product_code_reference = $row[1];
            $value = $row[2];
            $expiry_date = $row[3];
            $service_reference = $row[4];
            $voucher_count = $row[5];
            $IMEI = $row[6];
            $PCN = $row[7];
            $sim_number = $row[8];
            $IMSI = $row[9];
            $PUK = $row[10];

            // Validate the data
            $validator = Validator::make([
                'serial' => $serial,
                'product_code_reference' => $product_code_reference,
                'value' => $value,
                'expiry_date' => $expiry_date,
                'service_reference' => $service_reference,
                'voucher_count' => $voucher_count,
                'IMEI' => $IMEI,
                'PCN' => $PCN,
                'sim_number' => $sim_number,
                'IMSI' => $IMSI,
                'PUK' => $PUK
            ], [
                'serial' => 'nullable',
                'product_code_reference' => 'nullable|exists:product,product_code',
                'value' => 'required|integer',
                'expiry_date' => 'nullable|date_format:Y-m-d',
                'service_reference' => 'nullable|integer',
                'voucher_count' => 'required|integer|min:1',
                'IMEI' => 'nullable',
                'PCN' => 'nullable',
                'sim_number' => 'nullable',
                'IMSI' => 'nullable',
                'PUK' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response([
                    'message' => 'Invalid data in the file.',
                    'errors' => $validator->errors(),
                ], 400);
            }

            // Generate unique voucher codes
            for ($i = 0; $i < $voucher_count; $i++) {
                $voucherCodeExists = true;
                $voucherCode = '';

                while ($voucherCodeExists) {
                    $voucherCode = generateVoucherCode(16);
                    $voucherCodeExists = VoucherModel::where('voucher_code', $voucherCode)->exists();
                }

                $voucher = VoucherModel::create([
                    'serial' => $serial,
                    'product_code_reference' => $product_code_reference,
                    'voucher_code' => $voucherCode,
                    'value' => $value,
                    'expiry_date' => $expiry_date,
                    'available' => true,
                    'service_reference' => $service_reference,
                    'IMEI' => $IMEI,
                    'PCN' => $PCN,
                    'sim_number' => $sim_number,
                    'IMSI' => $IMSI,
                    'PUK' => $PUK,
                    'created_by' => 1,
                ]);

                $vouchers[] = $voucher;
            }
        }

        fclose($file);

        $history = new VoucherHistory();
        $history->user_id = 1;
        $history->transaction = "File created vouchers";
        $history->voucher_new_data = json_encode($vouchers);
        $history->save();

        return response([
            'message' => 'Vouchers created successfully',
            'results' => $vouchers,
        ], 200);
    }


    public function editVoucherByCode($voucherCode, Request $request)
    {
        $voucher = VoucherModel::where('voucher_code', $voucherCode)->first();
        $voucher_old = VoucherModel::where('voucher_code', $voucherCode)->get();

        if (!$voucher) {
            return response([
                'message' => "Voucher not found",
            ], 404);
        }

        $request->validate([
            'serial' => 'nullable',
            'product_code_reference' => 'nullable|exists:product,product_code',
            'value' => 'required|integer',
            'expiry_date' => 'nullable|date_format:Y-m-d',
            'available' => 'required|boolean',
            'service_reference' => 'nullable|integer',
            'IMEI' => 'nullable',
            'PCN' => 'nullable',
            'sim_number' => 'nullable',
            'IMSI' => 'nullable',
            'PUK' => 'nullable',
        ]);

        $voucher->update([
            'serial' => $request->serial,
            'product_code_reference' => $request->product_code_reference,
            'value' => $request->value,
            'expiry_date' => $request->expiry_date,
            'available' => $request->available,
            'service_reference' => $request->service_reference,
            'IMEI' => $request->IMEI,
            'PCN' => $request->PCN,
            'sim_number' => $request->sim_number,
            'IMSI' => $request->IMSI,
            'PUK' => $request->PUK,
        ]);

        $voucher_new = array(json_decode($voucher, true));

        if ($voucher_old != $voucher_new) {
            $history = new VoucherHistory();
            $history->user_id = 1;
            $history->transaction = "Edited voucher";
            $history->voucher_old_data = json_encode($voucher_old);
            $history->voucher_new_data = json_encode($voucher_new);
            $history->save();
        }

        return response([
            'message' => "Voucher edited successfully",
            'results' => $voucher
        ], 200);
    }

    public function setVoucherActive($voucherCode)
    {
        $voucher = VoucherModel::where('voucher_code', $voucherCode)->first();
        $voucher_old = VoucherModel::where('voucher_code', $voucherCode)->get();

        if (!$voucher) {
            return response([
                'message' => "Voucher not found",
            ], 404);
        }

        if ($voucher->available == true) {
            return response([
                'message' => "Voucher is already active",
            ], 404);
        }

        $voucher->available =true;
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
            'results' => $voucher
        ], 200);
    }

    public function setVoucherInactive($voucherCode)
    {
        $voucher = VoucherModel::where('voucher_code', $voucherCode)->first();
        $voucher_old = VoucherModel::where('voucher_code', $voucherCode)->get();

        if (!$voucher) {
            return response([
                'message' => "Voucher not found",
            ], 404);
        }

        if ($voucher->available == false) {
            return response([
                'message' => "Voucher is already inactive",
            ], 404);
        }

        $voucher->available = false;
        $voucher->save();
        $voucher_new = array(json_decode($voucher, true));

        $history = new VoucherHistory();
        $history->user_id = 1;
        $history->transaction = "Deactivated voucher";
        $history->voucher_old_data = json_encode($voucher_old);
        $history->voucher_new_data = json_encode($voucher_new);
        $history->save();

        return response([
            'message' => "Voucher set as inactive",
            'results' => $voucher
        ], 200);
    }

    public function massVoucherStatusActive(Request $request)
    {
        $voucher_code = $request->input('voucher_code');
        $available = true;

        $vouchers = VoucherModel::whereIn('voucher_code', $voucher_code)->get();
        $vouchers_old = VoucherModel::whereIn('voucher_code', $voucher_code)->get();

        if ($vouchers->isEmpty()) {
            return response()->json([
                'message' => 'Vouchers not found'
            ], 404);
        }

        foreach ($vouchers as $voucher) {
            $voucher->available = $available;
            $voucher->save();
        }
        $vouchers_new = $vouchers;

        $history = new VoucherHistory();
        $history->user_id = 1;
        $history->transaction = "Batch activated vouchers";
        $history->voucher_old_data = json_encode($vouchers_old);
        $history->voucher_new_data = json_encode($vouchers_new);
        $history->save();

        return response()->json([
            'message' => 'Voucher(s) updated successfully',
            'results' => $vouchers
        ], 200);
    }

    public function massVoucherStatusInactive(Request $request)
    {
        $voucher_code = $request->input('voucher_code');
        $available = false;

        $vouchers = VoucherModel::whereIn('voucher_code', $voucher_code)->get();
        $vouchers_old = VoucherModel::whereIn('voucher_code', $voucher_code)->get();

        if ($vouchers->isEmpty()) {
            return response()->json([
                'message' => 'Vouchers not found'
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
            'results' => $vouchers
        ], 200);
    }
}