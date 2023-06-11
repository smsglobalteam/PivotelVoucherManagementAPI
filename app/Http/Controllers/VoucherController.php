<?php

namespace App\Http\Controllers;

use App\Models\VoucherModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VoucherController extends Controller
{
    //
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
        $voucher = VoucherModel::where('voucher_code', $voucherCode)->get();

        if($voucher->isEmpty())
        {
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


}
