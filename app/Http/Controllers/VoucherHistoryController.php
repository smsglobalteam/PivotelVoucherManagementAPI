<?php

namespace App\Http\Controllers;

use App\Models\BatchOrderHistoryModel;
use App\Models\ProductHistoryModel;
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

    public function getProductHistory()
    {
        $productHistory = ProductHistoryModel::get();

        return response([
            'message' => "All Product history displayed successfully",
            'return_code' => '0',
            'results' => $productHistory,
        ], 200);
    }

    public function getBatchOrderHistory()
    {
        $batchOrderHistory = BatchOrderHistoryModel::get();

        return response([
            'message' => "All Batch Order history displayed successfully",
            'return_code' => '0',
            'results' => $batchOrderHistory,
        ], 200);
    }
}
