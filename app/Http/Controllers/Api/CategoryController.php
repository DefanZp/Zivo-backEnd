<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService) {
        $this->categoryService = $categoryService;
    }
    public function index() {
        $categories = $this->categoryService->getCategories();

        return response()->json([
            'success' => true,
            'message' => 'Data kategori berhasil diambil',
            'data' => $categories
        ], 200);
    }
}
