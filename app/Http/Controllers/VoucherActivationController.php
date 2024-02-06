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
                'message' => "Voucher is not active",
            ], 404);
        }

        if($voucher->expiry_date < date('Y-m-d'))
        {
            return response([
                'message' => "Voucher has expired",
            ], 404);
        }
   
        if($voucher->depleted == true)
        {
            return response([
                'message' => "Voucher is already depleted ",
            ], 404);
        }

        $voucher->depleted = true;
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
