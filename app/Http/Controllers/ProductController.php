<?php

namespace App\Http\Controllers;

use App\Models\ErrorCodesModel;
use App\Models\HistoryLogsModel;
use App\Models\ProductModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ErrorCodesController;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    //
    public function getAllProducts()
    {
        $products = ProductModel::orderBy('created_at', 'desc')
            ->leftJoin('voucher_main', function ($join) {
                $join->on('product.id', '=', 'voucher_main.product_id')
                    ->where('voucher_main.available', true)
                    ->whereNull('voucher_main.deplete_date')
                    ->where(function ($query) {
                        $query->whereNull('voucher_main.expiry_date')
                            ->orWhere('voucher_main.expiry_date', '>', now());
                    });
            })
            ->select('product.*', DB::raw('COUNT(voucher_main.id) as available_voucher_count'))
            ->groupBy('product.id')
            ->get();

        return response([
            'message' => "All products displayed successfully",
            'return_code' => '0',
            'results' => $products
        ], 200);
    }

    public function getProductByID($id)
    {
        $product = ProductModel::where('product_code', $id)->first();

        if (!$product) {
            return response([
                'message' => "Product not found",
                'return_code' => '-101',
            ], 404);
        }

        return response([
            'message' => "Product displayed successfully",
            'results' => $product
        ], 200);
    }

    public function createNewProduct(Request $request, ErrorCodesController $errorCodesController)
    {
        $validator = Validator::make($request->all(), [
            'product_code' => 'required|regex:/^\S*$/u|unique:product,product_code',
            'product_name' => 'required|unique:product,product_name',
            'supplier' => 'required',
            'threshold_alert' => 'nullable|integer',
            'order_amount' => 'nullable|integer',
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

        $product = ProductModel::create([
            'product_code' => $request->product_code,
            'product_name' => $request->product_name,
            'supplier' => $request->supplier,
            'threshold_alert' => $request->threshold_alert,
            'order_amount' => $request->order_amount,
            'created_by' => $request->attributes->get('preferred_username'),
        ]);

        $productHistory = new HistoryLogsModel();
        $productHistory->username = $request->attributes->get('preferred_username');
        $productHistory->transaction = "Created Product";
        $productHistory->database_table = "product";
        $productHistory->new_data = json_encode($product->toArray());
        $productHistory->save();

        return response([
            'message' => "Product created successfully",
            'return_code' => '0',
            'results' => $product
        ], 201);
    }

    public function editProductByID($id, Request $request, ErrorCodesController $errorCodesController)
    {
        $product = ProductModel::where('product_code', $id)->first();

        if (!$product) {
            return response([
                'message' => "Product not found",
                'return_code' => '-101',
            ], 404);
        }

        $product_old = clone $product;

        $validator = Validator::make($request->all(), [
            // 'product_code' => 'required|regex:/^\S*$/u|unique:product,product_code',
            // 'product_id' => 'required|integer|unique:product,product_id',
            'product_name' => 'required|unique:product,product_name,' . $product->id,
            'status' => 'required|boolean',
            'supplier' => 'required',
            'threshold_alert' => 'nullable|integer',
            'order_amount' => 'nullable|integer',
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

        $product->update([
            // 'product_code' => $request->product_code,
            // 'product_id' => $request->product_id,
            'product_name' => $request->product_name,
            'status' => $request->status,
            'supplier' => $request->supplier,
            'threshold_alert' => $request->threshold_alert,
            'order_amount' => $request->order_amount,
            'updated_by' => $request->attributes->get('preferred_username'),
        ]);

        $product->refresh();

        if ($product->wasChanged()) {
            $productHistory = new HistoryLogsModel();
            $productHistory->username = $request->attributes->get('preferred_username');
            $productHistory->transaction = "Edited Product";
            $productHistory->database_table = "product";
            $productHistory->old_data = json_encode($product_old->toArray());
            $productHistory->new_data = json_encode($product->toArray());
            $productHistory->save();
        }

        return response([
            'message' => "Product updated successfully",
            'return_code' => '0',
            'results' => $product
        ], 201);
    }

    public function deleteProductByID($id)
    {
        $product = ProductModel::where('product_code', $id)->first();

        if (!$product) {
            return response([
                'message' => "Product not found",
                'return_code' => '-101',
            ], 404);
        }

        $product->delete();

        return response([
            'message' => "Product deleted successfully",
            'return_code' => '0',
            'results' => $product
        ], 200);
    }
}
