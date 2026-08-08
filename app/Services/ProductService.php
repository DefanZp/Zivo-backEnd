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
        $validatedData = $this->validateProductData($data);

        return Product::create($validatedData);
    }

    public function updateProduct(int $id, array $data): Product
    {
        $product = $this->findProductById($id);

        $validatedData = $this->validateProductData($data, true);

        $product->update($validatedData);

        // mengambil ulang data dari database
        return $product->fresh();
    }

    public function deleteProduct(int $id): void
    {
        $product = $this->findProductById($id);

        $product->delete();
    }

    // private function

    private function validateProductData(array $data, bool $isUpdate = false): array
    {
        // Cek jika update, field tidak wajib, jika create field wajib
        if ($isUpdate) {
            $rules = [
                'category_id' => 'sometimes|exists:categories,id',
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'price' => 'sometimes|numeric|min:0',
                'stock' => 'sometimes|integer|min:0',
                'image_path' => 'sometimes|string|max:255',
            ];
        } else {
            $rules = [
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'image_path' => 'required|string|max:255',
            ];
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    // Cari product berdasarkan id
    private function findProductById(Int $id): Product
    {
        return Product::findOrFail($id);
    }
}
