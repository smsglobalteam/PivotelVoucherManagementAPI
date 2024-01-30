<?php

namespace App\Http\Controllers;

use App\Models\VoucherHistory;
use App\Models\VoucherModel;
use Illuminate\Http\Request;

class VoucherActivationController extends Controller
{
    //
    public function activateVoucher(Request $request)
    {

        $request->validate([
            'voucher_code' => 'required',
            'deplete' => 'required|boolean',
        ]);

        $voucherCode = $request->voucher_code;

        $voucher = VoucherModel::where('voucher_code', $voucherCode)->first();
        $voucher_old = VoucherModel::where('voucher_code', $voucherCode)->get();

        if(!$voucher)
        {
            return response([
                'message' => "Voucher not found",
            ], 404);
        }

        if($voucher->available == false)
        {
            return response([
                'message' => "Voucher has already been used",
            ], 404);
        }

        if($request->deplete == false)
        {
            return response([
                'message' => "Voucher is valid but deplete is set to false",
            ], 404);
        }


        $voucher->available = false;
        $voucher->save();
        
        $voucher_new = array(json_decode($voucher, true));

        $history = new VoucherHistory();
        $history->user_id = 1;
        $history->transaction = "Used voucher";
        $history->voucher_old_data = json_encode($voucher_old);
        $history->voucher_new_data = json_encode($voucher_new);
        $history->save();

        return response([
            'message' => "Voucher has been successfully activated",
            'results' => $voucher
        ], 200);
    }
}
