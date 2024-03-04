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
        $voucher = VoucherMainModel::where('voucher_code', $request->voucher_code)->first();
        $voucher_old = $voucher = VoucherMainModel::with(['voucherChildren' => function ($query) {$query->where('depleted', 0)->first();}
        ])
            ->where('voucher_code', $request->voucher_code)
            ->first();

        if (!$voucher) {
            return response([
                'message' => "Voucher not found",
                'return_code' => '-201',
            ], 404);
        }

        if ($voucher->available == false) {
            return response([
                'message' => "Voucher is not active",
                'return_code' => '-202',
            ], 404);
        }

        if ($voucher->expiry_date < date('Y-m-d')) {
            return response([
                'message' => "Voucher has expired",
                'return_code' => '-203',
            ], 404);
        }

        $voucherChild = $voucher->voucherChildren()->where('depleted', 0)->first();

        if ($voucherChild) {
            $voucherChild->update([
                'depleted' => true,
                'depleted_date' => now(),
                'serviceID' => $request->serviceID,
                'business_unit' => $request->business_unit,
                'serial_number' => $request->serial_number
            ]);

            $voucherRefreshed = $voucher->fresh([
                'voucherChildren' => function ($query) use ($voucherChild) {
                    $query->where('id', $voucherChild->id);
                }
            ]);

            $history = new VoucherHistory();
            $history->user_id = 1;
            $history->transaction = "Used voucher";
            $history->voucher_old_data = json_encode($voucher_old);
            $history->voucher_new_data = json_encode($voucherRefreshed);
            $history->save();

            return response([
                'message' => "Voucher associated successfully",
                'return_code' => '0',
                'results' => $voucherRefreshed,
            ], 200);
        } else {
            return response([
                'message' => "No available voucher children to associate",
                'return_code' => '1',
            ], 404);
        }
    }

    public function activateVoucher(Request $request)
    {

        $request->validate([
            'voucher_code' => 'required',
        ]);

        $voucherCode = $request->voucher_code;

        $voucher = VoucherMainModel::with([
            'voucherChildren' => function ($query) {
                $query->where('depleted', 0)->first();
            }
        ])
            ->where('voucher_code', $request->voucher_code)
            ->get();

        $voucher_old = VoucherMainModel::with([
            'voucherChildren' => function ($query) {
                $query->where('depleted', 0)->first();
            }
        ])
            ->where('voucher_code', $request->voucher_code)
            ->get();

        if (!$voucher) {
            return response([
                'message' => "Voucher not found",
                'return_code' => '-201',
            ], 404);
        }

        if ($voucher->expiry_date < date('Y-m-d')) {
            return response([
                'message' => "Voucher has expired",
                'return_code' => '-203',
            ], 404);
        }

        if ($voucher->depleted == true) {
            return response([
                'message' => "Voucher is already depleted ",
                'return_code' => '-204',
            ], 404);
        }

        if ($voucher->available == false) {
            return response([
                'message' => "Voucher is not active",
                'return_code' => '-202',
            ], 404);
        }

        $voucher->depleted = true;
        $voucher->depleted_date = now();

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
        ], 201);
    }
}
