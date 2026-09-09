<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Menjalankan seeder.
     */
    public function run(): void
    {
        $clothingId = Category::where('slug', 'clothing')->value('id');
        $electronicsId = Category::where('slug', 'electronics')->value('id');
        $accessoriesId = Category::where('slug', 'accessories')->value('id');

        $this->createProducts(
            $this->clothingProducts(),
            $clothingId
        );

        $this->createProducts(
            $this->electronicsProducts(),
            $electronicsId
        );

        $this->createProducts(
            $this->accessoriesProducts(),
            $accessoriesId
        );
    }

    /**
     * Membuat produk.
     */
    private function createProducts(
        array $products,
        int $categoryId
    ): void {
        foreach ($products as $product) {
            Product::create([
                'category_id' => $categoryId,
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'image_path' => $product['image_path'],
            ]);
        }
    }

    /**
     * Clothing
     */
    private function clothingProducts(): array
    {
        return [
            [
                'name' => 'Classic White T-Shirt',
                'description' => 'A simple and comfortable white t-shirt for everyday wear.',
                'price' => 120000,
                'stock' => 50,
                'image_path' => 'products/clothing-1.webp',
            ],
            [
                'name' => 'Oversized Black Hoodie',
                'description' => 'A comfortable oversized hoodie with a clean black design.',
                'price' => 275000,
                'stock' => 35,
                'image_path' => 'products/clothing-2.webp',
            ],
            [
                'name' => 'Slim Fit Jeans',
                'description' => 'Modern slim fit jeans suitable for casual everyday outfits.',
                'price' => 350000,
                'stock' => 19,
                'image_path' => 'products/clothing-3.webp',
            ],
            [
                'name' => 'Denim Jacket',
                'description' => 'A classic denim jacket designed for casual styling.',
                'price' => 425000,
                'stock' => 30,
                'image_path' => 'products/clothing-4.webp',
            ],
            [
                'name' => 'Casual Polo Shirt',
                'description' => 'A casual polo shirt with a clean and versatile design.',
                'price' => 185000,
                'stock' => 45,
                'image_path' => 'products/clothing-5.webp',
            ],
            [
                'name' => 'Basic Crewneck Sweatshirt',
                'description' => 'A simple crewneck sweatshirt for comfortable daily wear.',
                'price' => 220000,
                'stock' => 40,
                'image_path' => 'products/clothing-1.webp',
            ],
            [
                'name' => 'Cargo Pants',
                'description' => 'Durable cargo pants with multiple practical pockets.',
                'price' => 295000,
                'stock' => 32,
                'image_path' => 'products/clothing-2.webp',
            ],
            [
                'name' => 'Sport Shorts',
                'description' => 'Lightweight sport shorts designed for active movement.',
                'price' => 145000,
                'stock' => 55,
                'image_path' => 'products/clothing-3.webp',
            ],
            [
                'name' => 'Flannel Shirt',
                'description' => 'A casual flannel shirt with a comfortable everyday fit.',
                'price' => 210000,
                'stock' => 38,
                'image_path' => 'products/clothing-4.webp',
            ],
            [
                'name' => 'Cotton Jogger Pants',
                'description' => 'Soft cotton jogger pants suitable for casual activities.',
                'price' => 240000,
                'stock' => 28,
                'image_path' => 'products/clothing-5.webp',
            ],
            [
                'name' => 'V-Neck T-Shirt',
                'description' => 'A lightweight v-neck t-shirt with a simple modern style.',
                'price' => 135000,
                'stock' => 60,
                'image_path' => 'products/clothing-1.webp',
            ],
            [
                'name' => 'Oxford Shirt',
                'description' => 'A versatile oxford shirt suitable for casual and smart outfits.',
                'price' => 265000,
                'stock' => 25,
                'image_path' => 'products/clothing-2.webp',
            ],
            [
                'name' => 'Bomber Jacket',
                'description' => 'A lightweight bomber jacket with a modern casual look.',
                'price' => 390000,
                'stock' => 22,
                'image_path' => 'products/clothing-3.webp',
            ],
            [
                'name' => 'Windbreaker Jacket',
                'description' => 'A lightweight jacket designed to provide protection from wind.',
                'price' => 365000,
                'stock' => 27,
                'image_path' => 'products/clothing-4.webp',
            ],
            [
                'name' => 'Chino Pants',
                'description' => 'Classic chino pants with a clean and comfortable fit.',
                'price' => 285000,
                'stock' => 33,
                'image_path' => 'products/clothing-5.webp',
            ],
            [
                'name' => 'Linen Shirt',
                'description' => 'A lightweight linen shirt designed for warm weather.',
                'price' => 230000,
                'stock' => 42,
                'image_path' => 'products/clothing-1.webp',
            ],
            [
                'name' => 'Training Jacket',
                'description' => 'A lightweight training jacket designed for sports activities.',
                'price' => 315000,
                'stock' => 31,
                'image_path' => 'products/clothing-2.webp',
            ],
            [
                'name' => 'Graphic T-Shirt',
                'description' => 'A casual graphic t-shirt with a modern printed design.',
                'price' => 155000,
                'stock' => 48,
                'image_path' => 'products/clothing-3.webp',
            ],
            [
                'name' => 'Winter Hoodie',
                'description' => 'A warm hoodie designed for comfortable cold-weather wear.',
                'price' => 320000,
                'stock' => 20,
                'image_path' => 'products/clothing-4.webp',
            ],
            [
                'name' => 'Relax Fit T-Shirt',
                'description' => 'A relaxed fit t-shirt with a comfortable everyday silhouette.',
                'price' => 140000,
                'stock' => 52,
                'image_path' => 'products/clothing-5.webp',
            ],
        ];
    }

    /**
     * Electronics
     */
    private function electronicsProducts(): array
    {
        return [
            [
                'name' => 'Mechanical Keyboard',
                'description' => 'A mechanical keyboard designed for comfortable and responsive typing.',
                'price' => 850000,
                'stock' => 25,
                'image_path' => 'products/electronics-1.webp',
            ],
            [
                'name' => 'Wireless Mouse',
                'description' => 'A compact wireless mouse designed for everyday productivity.',
                'price' => 350000,
                'stock' => 40,
                'image_path' => 'products/electronics-2.webp',
            ],
            [
                'name' => 'Bluetooth Speaker',
                'description' => 'A portable Bluetooth speaker with clear and balanced sound.',
                'price' => 650000,
                'stock' => 30,
                'image_path' => 'products/electronics-3.webp',
            ],
            [
                'name' => 'Gaming Headset',
                'description' => 'A gaming headset designed for immersive audio and communication.',
                'price' => 950000,
                'stock' => 18,
                'image_path' => 'products/electronics-4.webp',
            ],
            [
                'name' => 'USB-C Hub',
                'description' => 'A versatile USB-C hub for connecting multiple peripheral devices.',
                'price' => 450000,
                'stock' => 35,
                'image_path' => 'products/electronics-5.webp',
            ],
            [
                'name' => '27 Inch Monitor',
                'description' => 'A 27-inch monitor suitable for productivity and entertainment.',
                'price' => 2850000,
                'stock' => 12,
                'image_path' => 'products/electronics-1.webp',
            ],
            [
                'name' => 'Portable SSD',
                'description' => 'A compact portable SSD for fast and reliable data storage.',
                'price' => 1250000,
                'stock' => 20,
                'image_path' => 'products/electronics-2.webp',
            ],
            [
                'name' => 'Wireless Charger',
                'description' => 'A convenient wireless charger for compatible smartphones and devices.',
                'price' => 325000,
                'stock' => 45,
                'image_path' => 'products/electronics-3.webp',
            ],
            [
                'name' => 'Power Bank 20000mAh',
                'description' => 'A high-capacity power bank designed for charging devices on the go.',
                'price' => 550000,
                'stock' => 28,
                'image_path' => 'products/electronics-4.webp',
            ],
            [
                'name' => 'Smart Watch',
                'description' => 'A modern smartwatch with useful everyday tracking features.',
                'price' => 1450000,
                'stock' => 15,
                'image_path' => 'products/electronics-5.webp',
            ],

            // Produk 11-20 bisa memakai pola yang sama.
        ];
    }

    /**
     * Accessories
     */
    private function accessoriesProducts(): array
    {
        return [
            [
                'name' => 'Leather Wallet',
                'description' => 'A compact leather wallet with a timeless everyday design.',
                'price' => 175000,
                'stock' => 40,
                'image_path' => 'products/accessories-1.webp',
            ],
            [
                'name' => 'Canvas Backpack',
                'description' => 'A durable canvas backpack suitable for everyday activities.',
                'price' => 325000,
                'stock' => 30,
                'image_path' => 'products/accessories-2.webp',
            ],
            [
                'name' => 'Baseball Cap',
                'description' => 'A casual baseball cap designed for everyday outdoor use.',
                'price' => 95000,
                'stock' => 55,
                'image_path' => 'products/accessories-3.webp',
            ],
            [
                'name' => 'Travel Pouch',
                'description' => 'A compact travel pouch for organizing small personal items.',
                'price' => 125000,
                'stock' => 45,
                'image_path' => 'products/accessories-4.webp',
            ],
            [
                'name' => 'Phone Stand',
                'description' => 'A simple phone stand for convenient viewing on a desk.',
                'price' => 75000,
                'stock' => 60,
                'image_path' => 'products/accessories-5.webp',
            ],
        ];
    }
}