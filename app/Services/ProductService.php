<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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

    private function processImage($image): string 
    {
        $manager = new ImageManager(new Driver());

        $image = $manager->read($image);

        $image->scaleDown(1200, 1200);

        $filename = uniqid('product_') . '.webp';

        Storage::disk('public')->put(
            'products/' . $filename,
            $image->toWebp(80)
        );

        return 'products/' . $filename;
    }

    public function createProduct(array $data): Product
    {
        if (isset($data['image'])) {
            $image = $data['image'];

            $imagePath = $this->processImage($image);

            $data['image_path'] = $imagePath;

            unset($data['image']);
        }

        return Product::create($data);
    }

    public function updateProduct(int $id, array $data): Product
    {

        $product = $this->findProductById($id);

        if (isset($data['image'])) {
            $image = $data['image'];

            $imagePath = $this->processImage($image);

            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $data['image_path'] = $imagePath;

            unset($data['image']);
        }

        $product->update($data);

        // mengambil ulang data dari database
        return $product->fresh();
    }

    public function deleteProduct(int $id): void
    {
        $product = $this->findProductById($id);

        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();
    }


    // Cari product berdasarkan id
    private function findProductById(Int $id): Product
    {
        return Product::findOrFail($id);
    }

    // cari product dengan stock rendah
    public function getLowStockProduct(int $threshold = 20)
    {
        return Product::where('stock', '<', $threshold)
            ->orderBy('stock')
            ->get();
    }
}
