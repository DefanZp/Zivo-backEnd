<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ){}

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_path' => 'required|string|max:255',
        ]);

        $product = $this->productService->createProduct(
            $validatedData
        );

        return response()->json([
            'message' => 'product created successfully',
            'data' => $product
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $validatedData = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'image_path' => 'sometimes|string|max:255',
        ]);

        $product = $this->productService->updateProduct(
            $id,
            $validatedData
        );

        return response()->json([
            'message' => 'product updated successfully',
            'data' => $product
        ], 200);
    }

    public function destroy(int $id)
    {
        $this->productService->deleteProduct($id);

        return response()->json([
            'message' => 'product deleted successfully'
        ], 200);
    }
}
