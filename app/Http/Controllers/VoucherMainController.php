<?php

namespace App\Http\Controllers;

use App\Models\VoucherChildModel;
use App\Models\VoucherHistory;
use App\Models\VoucherMainModel;
use Illuminate\Http\Request;

class VoucherMainController extends Controller
{
    //
    public function getAllVouchers()
    {
        $voucherHistory = VoucherMainModel::get();

        return response([
            'message' => "All voucher displayed successfully",
            'return_code' => '0',
            'results' => $voucherHistory,
        ], 200);
    }

    public function getVoucher($voucherCode)
    {
        $voucher = VoucherMainModel::with('voucherChildren')
                ->where('voucher_code', $voucherCode)
                ->get();

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
            'expiry_date' => 'nullable|date_format:Y-m-d',
            'voucher_count' => 'required|integer|min:1',
            'value' => 'required|integer',
        ]);


        $voucherMain = VoucherMainModel::create([
            'voucher_code' => $request->voucher_code,
            'product_code_reference' => $request->product_code_reference,
            'expiry_date' => $request->expiry_date,
            'voucher_count' => $request->voucher_count,
            'value' => $request->value,
            'created_by' => 1
        ]);

        $vouchers = [];
        $count = $request->voucher_count;

        // Generate voucher codes
        for ($i = 0; $i < $count; $i++) {

            $voucher = VoucherChildModel::create([
                'voucher_code_reference' => $request->voucher_code,
            ]);

            $vouchers[] = $voucher;
        }

        $history = new VoucherHistory();
        $history->user_id = 1;
        $history->transaction = "Created vouchers";
        $history->voucher_new_data = json_encode($vouchers);
        $history->save();

        return response([
            'message' => "Vouchers created successfully",
            'return_code' => '0',
            'results' => $vouchers
        ], 201);
    }

    public function editVoucher(Request $request, $voucherCode)
    {
        $request->validate([
            'product_code_reference' => 'nullable|exists:product,product_code',
            'value' => 'nullable|integer',
            'expiry_date' => 'nullable|date_format:Y-m-d',
        ]);

        $voucher = VoucherMainModel::where('voucher_code', $voucherCode)->first();

        if (!$voucher) {
            return response([
                'message' => "Voucher not found",
                'return_code' => '-201',
            ], 404);
        }

        $voucher->product_code_reference = $request->product_code_reference;
        $voucher->value = $request->value;
        $voucher->expiry_date = $request->expiry_date;
        $voucher->save();

        $history = new VoucherHistory();
        $history->user_id = 1;
        $history->transaction = "Updated voucher";
        $history->voucher_old_data = json_encode($voucher);
        $history->voucher_new_data = json_encode($request->all());
        $history->save();

        return response([
            'message' => "Voucher updated successfully",
            'return_code' => '0',
            'results' => $voucher
        ], 200);
    }

}
