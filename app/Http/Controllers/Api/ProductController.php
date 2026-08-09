<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function __construct(
        protected ProductService $productService
    ) 
    {}

    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->getAllProducts(

            $request->query('search'),

            $request->query('category'),

            $request->query('sort'),

            $request->query('direction'),
            
        );

        return response()->json([
            'success' => true,
            'message' => 'Data produk berhasil diambil',
            'data' => $products
        ], 200);
    }

    public function show(Int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);

        if (!$product) 
        {
            return response()->json([
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data produk berhasil diambil',
            'data' => $product
        ], 200);
    }
}
