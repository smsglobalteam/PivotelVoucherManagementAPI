<?php

namespace App\Http\Controllers;

use App\Models\HistoryLogsModel;
use Illuminate\Http\Request;

class HistoryLogsController extends Controller
{
    //
    public function getAllHistory()
    {
        $historyLogs = HistoryLogsModel::get();

        return response([
            'message' => "All history logs displayed successfully",
            'return_code' => '0',
            'results' => $historyLogs,
        ], 200);
    }

    public function getHistoryLogsByTable($database_table)
    {
        $historyLogs = HistoryLogsModel::where('database_table', $database_table)->get();

        return response([
            'message' => "Table history logs displayed successfully",
            'return_code' => '0',
            'results' => $historyLogs,
        ], 200);
    }
}
