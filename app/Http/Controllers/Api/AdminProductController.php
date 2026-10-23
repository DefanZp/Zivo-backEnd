<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ){}

    private function normalizeSpecifications(Request $request): void
    {
        if (!$request->exists('specifications')) {
            return;
        }

        $json = $request->input('specifications');
        $specifications = is_string($json)
            ? json_decode($json)
            : null;

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            !($specifications instanceof \stdClass)
        ) {
            throw ValidationException::withMessages([
                'specifications' => 'Specifications must be a valid JSON object.',
            ]);
        }

        $request->merge([
            'specifications' => (array) $specifications,
        ]);
    }

    public function store(Request $request)
    {
        $this->normalizeSpecifications($request);

        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,webp|max:5120',

            'specifications' => 'sometimes|array|max:30',
            'specifications.*' => 'required|string|max:1000',
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

        $this->normalizeSpecifications($request);

        $validatedData = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'image' => 'sometimes|image|mimes:jpeg,png,webp|max:5120',

            'specifications' => 'sometimes|array|max:30',
            'specifications.*' => 'required|string|max:1000',
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
