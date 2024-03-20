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

    public function createNewProduct(Request $request)
    {
        $request->validate([
            'product_code' => 'required|regex:/^\S*$/u|unique:product,product_code',
            'product_id' => 'required|integer|unique:product,product_id',
            'product_name' => 'required|unique:product,product_name',
            'supplier' => 'required',
        ]);

        $product = ProductModel::create([
            'product_code' => $request->product_code,
            'product_id' => $request->product_id,
            'product_name' => $request->product_name,
            'supplier' => $request->supplier,
            'created_by' => "user"
        ]);

        return response([
            'message' => "Product created successfully",
            'return_code' => '0',
            'results' => $product
        ], 201);
    }

    public function editProductByID($id, Request $request)
    {
        $product = ProductModel::where('product_code', $id)->first();

        if (!$product) {
            return response([
                'message' => "Product not found",
                'return_code' => '-101',
            ], 404);
        }

        $request->validate([
            // 'product_code' => 'required|regex:/^\S*$/u|unique:product,product_code',
            // 'product_id' => 'required|integer|unique:product,product_id',
            'product_name' => 'required|unique:product,product_name,'. $product->id,
            'supplier' => 'required',
        ]);


        $product ->update([
            // 'product_code' => $request->product_code,
            // 'product_id' => $request->product_id,
            'product_name' => $request->product_name,
            'supplier' => $request->supplier,
            'updated_by' => "user"
        ]);

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