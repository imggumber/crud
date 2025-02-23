<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index() {
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

    public function store() {}

    public function show() {}

    public function update() {}

    public function destroy() {}
}
