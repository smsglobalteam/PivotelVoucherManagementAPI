<?php

namespace App\Http\Controllers;

use App\Models\VoucherHistory;
use Illuminate\Http\Request;

class VoucherHistoryController extends Controller
{
    //
    public function getAllHistory()
    {
        $voucherHistory = VoucherHistory::get();

        return response([
            'message' => "All voucher history displayed successfully",
            'return_code' => '0',
            'results' => $voucherHistory,
        ], 200);
    }
}
