<?php

namespace App\Http\Controllers;

use App\Models\VoucherModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

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

        return response([
            'message' => "All vouchers displayed successfully",
            'results' => $vouchers
        ], 200);
    }

    public function getVoucherByCode($voucherCode)
    {
        $voucher = VoucherModel::where('voucher_code', $voucherCode)->first();

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

    public function createNewVoucher(Request $request)
    {
        $request->validate([
            'value' => 'required|integer',
            'expiry_date' => 'nullable|date_format:Y-m-d',
            'service_reference' => 'nullable',
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

        $voucherCodeExists = true;
        $voucherCode = '';

        // Generate a unique voucher code
        while ($voucherCodeExists) {
            $voucherCode = generateVoucherCode(16);

            $voucherCodeExists = VoucherModel::where('voucher_code', $voucherCode)->exists();
        }

        $voucher = VoucherModel::create([
            'voucher_code' => $voucherCode,
            'value' => $request->value,
            'expiry_date' => $request->expiry_date,
            'status' => 'active',
            'service_reference' => $request->service_reference,
            'created_by' => 1
        ]);

        return response([
            'message' => "Voucher created successfully",
            'results' => $voucher
        ], 200);
    }

    public function createNewVoucherMultple(Request $request)
    {
        $request->validate([
            'value' => 'required|integer',
            'expiry_date' => 'nullable|date_format:Y-m-d',
            'service_reference' => 'nullable',
            'voucher_count' => 'required|integer|min:1',
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
                'voucher_code' => $voucherCode,
                'value' => $request->value,
                'expiry_date' => $request->expiry_date,
                'status' => 'active',
                'service_reference' => $request->service_reference,
                'created_by' => 1
            ]);

            $vouchers[] = $voucher;
        }

        return response([
            'message' => "Vouchers created successfully",
            'results' => $vouchers
        ], 200);
    }

    public function createNewVoucherArray(Request $request)
    {
        $request->validate([
            '*.value' => 'required|integer',
            '*.expiry_date' => 'nullable|date_format:Y-m-d',
            '*.service_reference' => 'nullable',
            '*.voucher_count' => 'required|integer|min:1',
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
                    'voucher_code' => $voucherCode,
                    'value' => $entry['value'],
                    'expiry_date' => $entry['expiry_date'],
                    'status' => 'active',
                    'service_reference' => $entry['service_reference'],
                    'created_by' => 1
                ]);

                $vouchers[] = $voucher;
            }
        }

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
        $value = $row[0];
        $expiry_date = $row[1];
        $service_reference = $row[2];
        $voucher_count = $row[3];

        // Validate the data
        $validator = Validator::make([
            'value' => $value,
            'expiry_date' => $expiry_date,
            'service_reference' => $service_reference,
            'voucher_count' => $voucher_count,
        ], [
            'value' => 'required|integer',
            'expiry_date' => 'nullable|date_format:Y-m-d',
            'service_reference' => 'nullable',
            'voucher_count' => 'required|integer|min:1',
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
                'voucher_code' => $voucherCode,
                'value' => $value,
                'expiry_date' => $expiry_date,
                'status' => 'active',
                'service_reference' => $service_reference,
                'created_by' => 1,
            ]);

            $vouchers[] = $voucher;
        }
    }

    fclose($file);

    return response([
        'message' => 'Vouchers created successfully',
        'results' => $vouchers,
    ], 200);
}


    public function editVoucherByCode($voucherCode, Request $request)
    {
        $voucher = VoucherModel::where('voucher_code', $voucherCode)->first();

        if (!$voucher) {
            return response([
                'message' => "Voucher not found",
            ], 404);
        }

        $request->validate([
            'value' => 'required|integer',
            'expiry_date' => 'nullable|date_format:Y-m-d',
            'status' => 'required|in:active,inactive',
            'service_reference' => 'nullable',
        ]);

        $voucher->update([
            'value' => $request->value,
            'expiry_date' => $request->expiry_date,
            'status' => $request->status,
            'service_reference' => $request->service_reference,
        ]);

        return response([
            'message' => "Voucher displayed successfully",
            'results' => $voucher
        ], 200);
    }


    public function setVoucherInactive($voucherCode)
    {
        $voucher = VoucherModel::where('voucher_code', $voucherCode)->first();

        if (!$voucher) {
            return response([
                'message' => "Voucher not found",
            ], 404);
        }

        $voucher->status = 'inactive';
        $voucher->save();

        return response([
            'message' => "Voucher set as inactive",
            'results' => $voucher
        ], 200);
    }

}
