<?php

namespace App\Http\Controllers;

use App\Models\VoucherHistory;
use App\Models\VoucherMainModel;
use App\Models\VoucherModel;
use Illuminate\Http\Request;

class VoucherActivationController extends Controller
{
    //
    public function consumeVoucher(Request $request)
    {
        $request->validate([
            'serial' => 'required',
            'product_id' => 'required',
            'business_unit' => 'required',
            'service_reference' => 'required',
        ]);

        $voucher = VoucherModel::where('serial', $request->serial)
            ->first();

        if (!$voucher) {
            return response([
                'message' => "Voucher not found.",
                'return_code' => '-201',
            ], 404);
        }

        if ($voucher->deplete_date != null) {
            return response([
                'message' => "This voucher has already been consumed.",
                'return_code' => '-204',
            ], 401);
        }

        if ($voucher->expire_date && $voucher->expire_date < date('Y-m-d')) {
            return response([
                'message' => "Voucher has expired.",
                'return_code' => '-203',
            ], 401);
        }

        if ($voucher->available == false) {
            return response([
                'message' => "Voucher is not active.",
                'return_code' => '-202',
            ], 401);
        }

        $mismatches = [];

        if ($voucher->product_id != $request->product_id) {
            $mismatches[] = "Product ID does not match the voucher's product.";
        }
    
        if (!empty($mismatches)) {
            return response([
                'message' => "Validation errors: " . implode(' ', $mismatches),
                'return_code' => '-209',
            ], 404);
        }

        $voucher->deplete_date = now();
        $voucher->available = false;
        $voucher->service_reference = $request->service_reference;
        $voucher->business_unit = $request->business_unit;
        $voucher->save();

        $history = new VoucherHistory();
        $history->user_id = 1;
        $history->transaction = "Consumed voucher";
        $history->voucher_old_data = json_encode($voucher);
        $history->save();
    
        return response([
            'message' => "Voucer consumed successfully",
            'return_code' => '0',
            'results' => $voucher,
        ], 200);
    }



    // OLD CODE --------------------------------
    // public function consumeVoucher(Request $request)
    // {
    //     $voucher = VoucherModel::where('voucher_code', $request->voucher_code)->first();
    //     $voucher_old = $voucher = VoucherModel::with(['voucherChildren' => function ($query) {$query->where('depleted', 0)->first();}
    //     ])
    //         ->where('voucher_code', $request->voucher_code)
    //         ->first();

    //     if (!$voucher) {
    //         return response([
    //             'message' => "Voucher not found",
    //             'return_code' => '-201',
    //         ], 404);
    //     }

    //     if ($voucher->available == false) {
    //         return response([
    //             'message' => "Voucher is not active",
    //             'return_code' => '-202',
    //         ], 404);
    //     }

    //     if ($voucher->expiry_date < date('Y-m-d')) {
    //         return response([
    //             'message' => "Voucher has expired",
    //             'return_code' => '-203',
    //         ], 404);
    //     }

    //     $voucherChild = $voucher->voucherChildren()->where('depleted', 0)->first();

    //     if ($voucherChild) {
    //         $voucherChild->update([
    //             'depleted' => true,
    //             'depleted_date' => now(),
    //             'serviceID' => $request->serviceID,
    //             'business_unit' => $request->business_unit,
    //             'serial_number' => $request->serial_number
    //         ]);

    //         $voucherRefreshed = $voucher->fresh([
    //             'voucherChildren' => function ($query) use ($voucherChild) {
    //                 $query->where('id', $voucherChild->id);
    //             }
    //         ]);

    //         $history = new VoucherHistory();
    //         $history->user_id = 1;
    //         $history->transaction = "Used voucher";
    //         $history->voucher_old_data = json_encode($voucher_old);
    //         $history->voucher_new_data = json_encode($voucherRefreshed);
    //         $history->save();

    //         return response([
    //             'message' => "Voucher associated successfully",
    //             'return_code' => '0',
    //             'results' => $voucherRefreshed,
    //         ], 200);
    //     } else {
    //         return response([
    //             'message' => "No available voucher children to associate",
    //             'return_code' => '1',
    //         ], 404);
    //     }
    // }

    // public function activateVoucher(Request $request)
    // {

    //     $request->validate([
    //         'voucher_code' => 'required',
    //     ]);

    //     $voucherCode = $request->voucher_code;

    //     $voucher = VoucherModel::with([
    //         'voucherChildren' => function ($query) {
    //             $query->where('depleted', 0)->first();
    //         }
    //     ])
    //         ->where('voucher_code', $request->voucher_code)
    //         ->get();

    //     $voucher_old = VoucherModel::with([
    //         'voucherChildren' => function ($query) {
    //             $query->where('depleted', 0)->first();
    //         }
    //     ])
    //         ->where('voucher_code', $request->voucher_code)
    //         ->get();

    //     if (!$voucher) {
    //         return response([
    //             'message' => "Voucher not found",
    //             'return_code' => '-201',
    //         ], 404);
    //     }

    //     if ($voucher->expiry_date < date('Y-m-d')) {
    //         return response([
    //             'message' => "Voucher has expired",
    //             'return_code' => '-203',
    //         ], 404);
    //     }

    //     if ($voucher->depleted == true) {
    //         return response([
    //             'message' => "Voucher is already depleted ",
    //             'return_code' => '-204',
    //         ], 404);
    //     }

    //     if ($voucher->available == false) {
    //         return response([
    //             'message' => "Voucher is not active",
    //             'return_code' => '-202',
    //         ], 404);
    //     }

    //     $voucher->depleted = true;
    //     $voucher->depleted_date = now();

    //     $voucher->save();

    //     $voucher_new = array(json_decode($voucher, true));

    //     $history = new VoucherHistory();
    //     $history->user_id = 1;
    //     $history->transaction = "Used voucher";
    //     $history->voucher_old_data = json_encode($voucher_old);
    //     $history->voucher_new_data = json_encode($voucher_new);
    //     $history->save();

    //     return response([
    //         'message' => "Voucher has been successfully activated",
    //         'results' => $voucher
    //     ], 201);
    // }
}
