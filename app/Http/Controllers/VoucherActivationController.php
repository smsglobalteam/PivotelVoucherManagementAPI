<?php

namespace App\Http\Controllers;

use App\Models\VoucherModel;
use Illuminate\Http\Request;

class VoucherActivationController extends Controller
{
    //
    public function activateVoucher($voucherCode)
    {
        $voucher = VoucherModel::where('voucher_code', $voucherCode)->first();

        if(!$voucher)
        {
            return response([
                'message' => "Voucher not found",
            ], 404);
        }

        if($voucher->status == "inactive")
        {
            return response([
                'message' => "Voucher is inactive",
            ], 404);
        }

        $voucher->status = "used";
        $voucher->save();

        return response([
            'message' => "Voucher has been successfully activated",
            'results' => $voucher
        ], 200);
    }


}
