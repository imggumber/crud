<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::get();
        if ($products->count() > 0) {
            $product = ProductResource::collection($products);
            return response()->json([
                'message' => 'All products fetched successfully',
                'data' => $product,
            ], 200);
        } else {
            return response()->json([
                'message' => 'No product available',
            ], 200);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator($request->all(), [
            'name' => 'required|max:255|string',
            'description' => 'required',
            'price' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Product added successfully',
                'data' => new ProductResource($product),
            ], 201);
        } catch (\Exception $e) {
            Log::alert($e->getMessage());
            DB::rollBack();
            return response()->json([
                'message' => 'Internal server error',
            ], 500);
        }
    }

    public function show() {}

    public function update() {}

    public function destroy() {}
}
