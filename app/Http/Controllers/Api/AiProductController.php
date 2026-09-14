<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;

class AiProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function index(Request $request)
    {
        $products = $this->productService->searchProductsForAI(
            $request->query('search'),
            $request->query('max_price'),
            $request->boolean('in_stock')
        );

        return response()->json([
            'success' => true,
            'message' => 'Data produk berhasil diambil',
            'data' => $products,
        ]);
    }
}