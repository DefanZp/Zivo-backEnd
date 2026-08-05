<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function store(Request $request)
    {
        $product = $this->productService->createProduct(
            $request->all()
        );

        return response()->json([
            'message' => 'product created successfully',
            'data' => $product
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $product = $this->productService->updateProduct(
            $id,
            $request->all()
        );

        return response()->json([
            'message' => 'product updated successfully',
            'data' => $product
        ]);
    }

    public function destroy(int $id)
    {
        $this->productService->deleteProduct($id);

        return response()->json([
            'message' => 'product deleted successfully'
        ]);
    }
}
