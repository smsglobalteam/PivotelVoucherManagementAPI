<?php

namespace App\Http\Controllers;

use App\Models\ProductModel;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    public function getAllProducts()
    {
        $products = ProductModel::get();

        return response([
            'message' => "All products displayed successfully",
            'results' => $products
        ], 200);
    }

    public function getProductByID($id)
    {
        $product = ProductModel::where('product_code', $id)->with('voucher')->first();

        if (!$product) {
            return response([
                'message' => "Product not found",
            ], 404);
        }

        return response([
            'message' => "Product displayed successfully",
            'results' => $product
        ], 200);
    }

    public function createNewProduct(Request $request)
    {
        $request->validate([
            'product_code' => 'required|unique:product,product_code',
            'product_type' => 'nullable',
            'product_name' => 'required',
        ]);

        $product = ProductModel::create([
            'product_code' => $request->product_code,
            'product_type' => $request->product_type,
            'product_name' => $request->product_name,
            'created_by' => 1
        ]);

        return response([
            'message' => "Product created successfully",
            'results' => $product
        ], 200);
    }

    public function editProductByID($id, Request $request)
    {
        $product = ProductModel::where('product_code', $id)->with('voucher')->first();

        if (!$product) {
            return response([
                'message' => "Product not found",
            ], 404);
        }

        $request->validate([
            // 'product_code' => 'required|unique:product,product_code,' . $product->id,
            'product_type' => 'nullable',
            'product_name' => 'required',
        ]);

        $product ->update([
            // 'product_code' => $request->product_code,
            'product_type' => $request->product_type,
            'product_name' => $request->product_name,
            'created_by' => 1
        ]);

        return response([
            'message' => "Product updated successfully",
            'results' => $product
        ], 200);
    }

    public function deleteProductByID($id)
    {
        $product = ProductModel::where('product_code', $id)->with('voucher')->first();

        if (!$product) {
            return response([
                'message' => "Product not found",
            ], 404);
        }

        $product->delete();

        return response([
            'message' => "Product deleted successfully",
            'results' => $product
        ], 200);
    }
}