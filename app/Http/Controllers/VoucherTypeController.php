<?php

namespace App\Http\Controllers;

use App\Models\HistoryLogsModel;
use App\Models\VoucherModel;
use App\Models\VoucherTypeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VoucherTypeController extends Controller
{
    //
    public function getAllVoucherType()
    {
        $voucherType = VoucherTypeModel::query()
            ->leftJoin('product', 'voucher_type.product_id', '=', 'product.id')
            ->leftJoin('voucher_main', function ($join) {
                $join->on('product.id', '=', 'voucher_main.product_id')
                    ->where('voucher_main.available', '=', true)
                    ->whereNull('voucher_main.deplete_date')
                    ->where(function ($query) {
                        $query->whereNull('voucher_main.expiry_date')
                            ->orWhere('voucher_main.expiry_date', '>', now());
                    });
            })
            ->select(
                'voucher_type.*',
                'product.product_name as product_name',
                'product.threshold_alert',
                DB::raw('COUNT(voucher_main.id) as available_voucher_count'),
            )
            ->groupBy(
                'voucher_type.id',
                'product.product_name',
                'product.threshold_alert'
            )
            ->get();

        return response([
            'message' => "All voucher type displayed successfully",
            'return_code' => '0',
            'results' => $voucherType
        ], 200);
    }

    public function getAllVoucherByID($id)
    {
        $voucherType = VoucherTypeModel::where('voucher_code', $id)->first();

        if (!$voucherType) {
            return response([
                'message' => "Voucher type not found",
                'return_code' => '-101',
            ], 404);
        }

        return response([
            'message' => "Voucher type displayed successfully",
            'results' => $voucherType
        ], 200);
    }

    public function createNewVoucherType(Request $request, ErrorCodesController $errorCodesController)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:product,id',
            'voucher_code' => 'required|regex:/^\S*$/u|unique:voucher_type,voucher_code',
            'voucher_name' => 'required',
        ]);

        if ($validator->fails()) {
            // Map validation errors to custom codes
            $customErrorCodes = $errorCodesController->mapValidationErrorsToCustomCodes($validator);

            // Fetch custom error messages from the database
            $errorMessages = $errorCodesController->getErrorMessagesFromCodes($customErrorCodes);

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $errorMessages,
            ], 422);
        }

        $voucherType = VoucherTypeModel::create([
            'product_id' => $request->product_id,
            'voucher_code' => $request->voucher_code,
            'voucher_name' => $request->voucher_name,
            'created_by' => $request->attributes->get('preferred_username'),
        ]);

        $voucherTypeHistory = new HistoryLogsModel();
        $voucherTypeHistory->username = $request->attributes->get('preferred_username');
        $voucherTypeHistory->transaction = "Created Voucher Type";
        $voucherTypeHistory->database_table = "voucher_type";
        $voucherTypeHistory->new_data = json_encode($voucherType->toArray());
        $voucherTypeHistory->save();

        return response([
            'message' => "Voucher type created successfully",
            'return_code' => '0',
            'results' => $voucherType
        ], 201);
    }

    public function editVoucherTypeByCode($id, Request $request, ErrorCodesController $errorCodesController)
    {
        $voucherType = VoucherTypeModel::where('voucher_code', $id)->first();

        if (!$voucherType) {
            return response([
                'message' => "Voucher type not found",
                'return_code' => '-101',
            ], 404);
        }

        $voucher_type_old = clone $voucherType;

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:product,id',
            'status' => 'required|boolean',
            'voucher_name' => 'required',
        ]);

        if ($validator->fails()) {
            // Map validation errors to custom codes
            $customErrorCodes = $errorCodesController->mapValidationErrorsToCustomCodes($validator);

            // Fetch custom error messages from the database
            $errorMessages = $errorCodesController->getErrorMessagesFromCodes($customErrorCodes);

            return response()->json([
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 422);
        }

        $voucherMain = VoucherModel::where('voucher_type_id', $voucherType->id)->get();

        $voucherType->update([
            'product_id' => $request->product_id,
            'status' => $request->status,
            'voucher_name' => $request->voucher_name,
            'updated_by' => $request->attributes->get('preferred_username'),
        ]);

        $voucherType->refresh();

        if ($voucherType->wasChanged()) {
            $voucherTypeHistory = new HistoryLogsModel();
            $voucherTypeHistory->username = $request->attributes->get('preferred_username');
            $voucherTypeHistory->transaction = "Edited Voucher Type";
            $voucherTypeHistory->database_table = "voucher_type";
            $voucherTypeHistory->old_data = json_encode($voucher_type_old->toArray());
            $voucherTypeHistory->new_data = json_encode($voucherType->toArray());
            $voucherTypeHistory->save();
        }

        VoucherModel::where('voucher_type_id', $voucherType->id)
            ->update(['product_id' => $request->product_id]);

        return response([
            'message' => "Voucher type updated successfully",
            'return_code' => '0',
            'results' => $voucherType,
        ], 201);
    }
}
