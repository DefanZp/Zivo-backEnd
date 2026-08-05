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
        // Membuat instance Faker
        $faker = fake();

        // Ambil ID kategori berdasarkan slug
        $clothingId = Category::where('slug', 'clothing')->value('id');
        $electronicsId = Category::where('slug', 'electronics')->value('id');
        $accessoriesId = Category::where('slug', 'accessories')->value('id');

        // Generate produk setiap kategori
        $this->createProducts(
            $this->clothingProducts(),
            $this->clothingImages(),
            $clothingId,
            $faker,
            90000,
            500000
        );

        $this->createProducts(
            $this->electronicsProducts(),
            $this->electronicsImages(),
            $electronicsId,
            $faker,
            250000,
            4000000
        );

        $this->createProducts(
            $this->accessoriesProducts(),
            $this->accessoriesImages(),
            $accessoriesId,
            $faker,
            50000,
            400000
        );
    }

    /**
     * Membuat produk berdasarkan daftar nama.
     */
    private function createProducts(
    array $products,
    array $images,
    int $categoryId,
    $faker,
    int $minPrice,
    int $maxPrice
    ): void {

        foreach ($products as $productName) {

            Product::create([

                'category_id' => $categoryId,

                'name' => $productName,

                'description' => $faker->paragraph(3),

                'price' => $faker->numberBetween(
                    $minPrice,
                    $maxPrice
                ),

                'stock' => $faker->numberBetween(
                    10,
                    100
                ),

                'image_path' => $faker->randomElement($images)

            ]);

        }

    }

    /**
     * Clothing
     */
    private function clothingProducts(): array
    {
        return [

            'Classic White T-Shirt',
            'Oversized Black Hoodie',
            'Slim Fit Jeans',
            'Denim Jacket',
            'Casual Polo Shirt',
            'Basic Crewneck Sweatshirt',
            'Cargo Pants',
            'Sport Shorts',
            'Flannel Shirt',
            'Cotton Jogger Pants',

            'V-Neck T-Shirt',
            'Oxford Shirt',
            'Bomber Jacket',
            'Windbreaker Jacket',
            'Chino Pants',
            'Linen Shirt',
            'Training Jacket',
            'Graphic T-Shirt',
            'Winter Hoodie',
            'Relax Fit T-Shirt',

        ];
    }

    /**
     * Electronics
     */
    private function electronicsProducts(): array
    {
        return [

            'Mechanical Keyboard',
            'Wireless Mouse',
            'Bluetooth Speaker',
            'Gaming Headset',
            'USB-C Hub',
            '27 Inch Monitor',
            'Portable SSD',
            'Wireless Charger',
            'Power Bank 20000mAh',
            'Smart Watch',

            'Laptop Cooling Pad',
            'RGB Mouse Pad',
            'HD Webcam',
            'Noise Cancelling Earbuds',
            'Bluetooth Keyboard',
            'External Hard Drive',
            'USB Flash Drive',
            'Gaming Controller',
            'Mini Projector',
            'WiFi Router',

        ];
    }

    /**
     * Accessories
     */
    private function accessoriesProducts(): array
    {
        return [

            'Leather Wallet',
            'Canvas Backpack',
            'Baseball Cap',
            'Travel Pouch',
            'Phone Stand',
            'Laptop Sleeve',
            'Leather Belt',
            'Key Organizer',
            'Card Holder',
            'Stainless Bottle',

            'Gym Duffel Bag',
            'Passport Holder',
            'Sling Bag',
            'Desk Organizer',
            'Notebook Cover',
            'Umbrella',
            'Travel Adapter',
            'Camera Strap',
            'Sunglasses',
            'Mini Tripod',

        ];
    }

    private function clothingImages(): array
    {
        return [
            'products/clothing-1.jpg',
            'products/clothing-2.jpg',
            'products/clothing-3.jpg',
            'products/clothing-4.jpg',
            'products/clothing-5.jpg',
        ];
    }

    private function electronicsImages(): array
    {
        return [
            'products/electronics-1.jpg',
            'products/electronics-2.jpg',
            'products/electronics-3.jpg',
            'products/electronics-4.jpg',
            'products/electronics-5.jpg',
        ];
    }

    private function accessoriesImages(): array
    {
        return [
            'products/accessories-1.jpg',
            'products/accessories-2.jpg',
            'products/accessories-3.jpg',
            'products/accessories-4.jpg',
            'products/accessories-5.jpg',
        ];
    }
}