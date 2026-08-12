<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductService
{

    public function getAllProducts(
        ?string $search = null, 
        ?int $category = null,
        ?string $sort = null, 
        ?string $direction = null,
    )

    {
        $query = Product::with('category');

        if ($search) {
            
            $searchLower = strtoLower($search);

            $query->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"]);
        }

        $allowedSortColumn = ['price', 'name', 'created_at' ];
        $allowedDirection = ['asc', 'desc'];

        // Cek apakah sort valid
        if ($sort && in_array($sort, $allowedSortColumn)) {
            
            // cek apakah direction valid
            $sortDirection = in_array($direction, $allowedDirection)
                ? $direction
                : 'asc';

            $query->orderBy($sort, $sortDirection);

        }

        // Cek apakah ada parameter category
        if ($category) {
            $query->where('category_id', $category);
        }

        else {
            // Jika tidak ada parameter sort
            $query->latest()->get();
        }

        return $query->paginate(12);
    }

    public function getProductById(int $id): ?Product
    {
        return Product::with('category')->find($id);
    }


    // Untuk admin
    public function createProduct(array $data): Product
    {
        return Product::create($data);
    }

    public function updateProduct(int $id, array $data): Product
    {
        $product = $this->findProductById($id);

        $product->update($data);

        // mengambil ulang data dari database
        return $product->fresh();
    }

    public function deleteProduct(int $id): void
    {
        $product = $this->findProductById($id);

        $product->delete();
    }


    // Cari product berdasarkan id
    private function findProductById(Int $id): Product
    {
        return Product::findOrFail($id);
    }
}
