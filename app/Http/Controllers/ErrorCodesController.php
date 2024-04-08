<?php

namespace App\Http\Controllers;

use App\Models\ErrorCodesModel;
use App\Models\ProductHistoryModel;
use Illuminate\Http\Request;

class ErrorCodesController extends Controller
{
    //
    public function getAllErorrCodes()
    {
        $errorCodes = ErrorCodesModel::get();

        return response([
            'message' => "All error codes displayed successfully",
            'return_code' => '0',
            'results' => $errorCodes
        ], 200);
    }

    public function getErrorCodeByID($id)
    {
        $errorCodes = ErrorCodesModel::where('error_code', $id)->first();

        if (!$errorCodes) {
            return response([
                'message' => "Error codes not found",
                'return_code' => '-501',
            ], 404);
        }

        return response([
            'message' => "Error code displayed successfully",
            'results' => $errorCodes
        ], 200);
    }

    public function getErrorMessages(Request $request)
    {
        $request->validate([
            'error_codes' => 'nullable',
        ]);

        $errorCodesArray = explode(',', $request->error_codes);

        $errorMessages = ErrorCodesModel::whereIn('error_code', $errorCodesArray)
            ->get(['error_code', 'error_message']);

        if ($errorMessages->isEmpty()) {
            return response()->json([
                'message' => "No error messages found for the provided codes.",
                'results' => []
            ], 404);
        }

        return response()->json([
            'message' => "Error codes processed successfully.",
            'return_code' => '0',
            'results' => $errorMessages
        ], 200);
    }

    public function createNewErrorCode(Request $request)
    {
        $request->validate([
            'error_code' => 'required|integer|unique:error_codes_reference,error_code',
            'error_message' => 'required|string',
        ]);

        $errorCodes = ErrorCodesModel::create([
            'error_code' => $request->error_code,
            'error_message' => $request->error_message,
            'created_by' => $request->attributes->get('preferred_username'),
        ]);

        // $productHistory = new ProductHistoryModel();
        // $productHistory->user_id = $request->attributes->get('preferred_username');
        // $productHistory->transaction = "Created Error Code";
        // $productHistory->product_new_data = json_encode($errorCodes->toArray());
        // $productHistory->save();

        return response([
            'message' => "Error codes created successfully",
            'return_code' => '0',
            'results' => $errorCodes
        ], 201);
    }

    public function editErrorByCode($id, Request $request)
    {
        $errorCodesReference = ErrorCodesModel::where('id', $id)->first();

        if (!$errorCodesReference) {
            return response([
                'message' => "Error code not found",
                'return_code' => '-101',
            ], 404);
        }

        $errorCodesReference_old = clone $errorCodesReference;

        $request->validate([
            'error_code' => 'required|integer|unique:error_codes_reference,error_code,' . $errorCodesReference->id,
            'error_message' => 'required|string',
        ]);

        $errorCodesReference->update([
            'error_code' => $request->error_code,
            'error_message' => $request->error_message,
            'updated_by' => $request->attributes->get('preferred_username'),
        ]);

        $errorCodesReference->refresh();

        // if ($product->wasChanged()) { 
        //     $productHistory = new ProductHistoryModel();
        //     $productHistory->user_id = $request->attributes->get('preferred_username');
        //     $productHistory->transaction = "Edited Error Code";
        //     $productHistory->product_old_data = json_encode($product_old->toArray());
        //     $productHistory->product_new_data = json_encode($product->toArray());
        //     $productHistory->save();
        // }

        return response([
            'message' => "Error code updated successfully",
            'return_code' => '0',
            'results' => $errorCodesReference
        ], 201);
    }

    public function deleteErrorCodeByID($id)
    {
        $errorCodesReference = ErrorCodesModel::where('error_code', $id)->first();

        if (!$errorCodesReference) {
            return response([
                'message' => "Error code not found",
                'return_code' => '-101',
            ], 404);
        }

        $errorCodesReference->delete();

        return response([
            'message' => "Error code deleted successfully",
            'return_code' => '0',
            'results' => $errorCodesReference
        ], 200);
    }
}
