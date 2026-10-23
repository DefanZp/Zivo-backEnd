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
            Product::updateOrCreate(
                [
                    'category_id' => $categoryId,
                    'name' => $product['name'],
                ],
                [
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'image_path' => $product['image_path'],
                    'specifications' => $product['specifications'] ?? [],
                ]
            );
        }
    }

    /**
     * Clothing products.
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
                'specifications' => [
                    'material' => 'Cotton',
                    'color' => 'White',
                    'fit' => 'Regular fit',
                    'style' => 'Minimalist casual',
                    'intended_use' => ['Everyday wear', 'Layering'],
                    'features' => ['Plain design', 'Short sleeves', 'Crew neck'],
                    'available_sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'care_instructions' => ['Machine wash cold', 'Wash with similar colors'],
                ],
            ],
            [
                'name' => 'Oversized Black Hoodie',
                'description' => 'A comfortable oversized hoodie with a clean black design.',
                'price' => 275000,
                'stock' => 35,
                'image_path' => 'products/clothing-2.webp',
                'specifications' => [
                    'material' => 'Cotton-polyester fleece blend',
                    'color' => 'Black',
                    'fit' => 'Oversized',
                    'style' => 'Streetwear casual',
                    'intended_use' => ['Everyday wear', 'Layering', 'Cool weather'],
                    'features' => ['Adjustable hood', 'Kangaroo pocket', 'Ribbed cuffs'],
                    'available_sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'care_instructions' => ['Machine wash cold', 'Turn inside out before washing'],
                ],
            ],
            [
                'name' => 'Slim Fit Jeans',
                'description' => 'Modern slim fit jeans suitable for casual everyday outfits.',
                'price' => 350000,
                'stock' => 19,
                'image_path' => 'products/clothing-3.webp',
                'specifications' => [
                    'material' => 'Denim cotton blend',
                    'color' => 'Dark blue',
                    'fit' => 'Slim fit',
                    'style' => 'Casual modern',
                    'intended_use' => ['Everyday wear', 'Casual outings'],
                    'features' => ['Five-pocket design', 'Button and zipper closure'],
                    'available_sizes' => ['28', '30', '32', '34', '36'],
                    'care_instructions' => ['Machine wash cold', 'Wash with similar colors'],
                ],
            ],
            [
                'name' => 'Denim Jacket',
                'description' => 'A classic denim jacket designed for casual styling.',
                'price' => 425000,
                'stock' => 30,
                'image_path' => 'products/clothing-4.webp',
                'specifications' => [
                    'material' => 'Denim cotton blend',
                    'color' => 'Blue',
                    'fit' => 'Regular fit',
                    'style' => 'Classic casual',
                    'intended_use' => ['Casual outings', 'Layering'],
                    'features' => ['Button-front closure', 'Chest pockets', 'Long sleeves'],
                    'available_sizes' => ['S', 'M', 'L', 'XL'],
                    'care_instructions' => ['Machine wash cold', 'Wash inside out'],
                ],
            ],
            [
                'name' => 'Casual Polo Shirt',
                'description' => 'A casual polo shirt with a clean and versatile design.',
                'price' => 185000,
                'stock' => 45,
                'image_path' => 'products/clothing-5.webp',
                'specifications' => [
                    'material' => 'Cotton pique',
                    'color' => 'Navy blue',
                    'fit' => 'Regular fit',
                    'style' => 'Smart casual',
                    'intended_use' => ['Everyday wear', 'Casual office', 'Casual outings'],
                    'features' => ['Collar', 'Button placket', 'Short sleeves'],
                    'available_sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'care_instructions' => ['Machine wash cold', 'Do not bleach'],
                ],
            ],
            [
                'name' => 'Basic Crewneck Sweatshirt',
                'description' => 'A simple crewneck sweatshirt for comfortable daily wear.',
                'price' => 220000,
                'stock' => 40,
                'image_path' => 'products/clothing-1.webp',
                'specifications' => [
                    'material' => 'Cotton-polyester fleece blend',
                    'color' => 'Heather gray',
                    'fit' => 'Regular fit',
                    'style' => 'Minimalist casual',
                    'intended_use' => ['Everyday wear', 'Layering', 'Cool weather'],
                    'features' => ['Crew neck', 'Long sleeves', 'Ribbed cuffs and hem'],
                    'available_sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'care_instructions' => ['Machine wash cold', 'Wash inside out'],
                ],
            ],
            [
                'name' => 'Cargo Pants',
                'description' => 'Durable cargo pants with multiple practical pockets.',
                'price' => 295000,
                'stock' => 32,
                'image_path' => 'products/clothing-2.webp',
                'specifications' => [
                    'material' => 'Cotton blend',
                    'color' => 'Olive green',
                    'fit' => 'Relaxed fit',
                    'style' => 'Utility casual',
                    'intended_use' => ['Everyday wear', 'Travel', 'Outdoor casual activities'],
                    'features' => ['Multiple cargo pockets', 'Belt loops', 'Zipper closure'],
                    'available_sizes' => ['28', '30', '32', '34', '36'],
                    'care_instructions' => ['Machine wash cold', 'Wash with similar colors'],
                ],
            ],
            [
                'name' => 'Sport Shorts',
                'description' => 'Lightweight sport shorts designed for active movement.',
                'price' => 145000,
                'stock' => 55,
                'image_path' => 'products/clothing-3.webp',
                'specifications' => [
                    'material' => 'Polyester',
                    'color' => 'Black',
                    'fit' => 'Athletic fit',
                    'style' => 'Sport',
                    'intended_use' => ['Workout', 'Running', 'Active movement'],
                    'features' => ['Elastic waistband', 'Drawstring', 'Side pockets'],
                    'available_sizes' => ['S', 'M', 'L', 'XL'],
                    'care_instructions' => ['Machine wash cold', 'Do not iron directly on printed areas'],
                ],
            ],
            [
                'name' => 'Flannel Shirt',
                'description' => 'A casual flannel shirt with a comfortable everyday fit.',
                'price' => 210000,
                'stock' => 38,
                'image_path' => 'products/clothing-4.webp',
                'specifications' => [
                    'material' => 'Cotton flannel',
                    'color' => 'Red and black check',
                    'fit' => 'Regular fit',
                    'style' => 'Casual plaid',
                    'intended_use' => ['Everyday wear', 'Casual outings', 'Layering'],
                    'features' => ['Check pattern', 'Button-front closure', 'Long sleeves'],
                    'available_sizes' => ['S', 'M', 'L', 'XL'],
                    'care_instructions' => ['Machine wash cold', 'Wash with similar colors'],
                ],
            ],
            [
                'name' => 'Cotton Jogger Pants',
                'description' => 'Soft cotton jogger pants suitable for casual activities.',
                'price' => 240000,
                'stock' => 28,
                'image_path' => 'products/clothing-5.webp',
                'specifications' => [
                    'material' => 'Cotton blend',
                    'color' => 'Gray',
                    'fit' => 'Tapered jogger fit',
                    'style' => 'Casual athleisure',
                    'intended_use' => ['Everyday wear', 'Travel', 'Relaxing at home'],
                    'features' => ['Elastic waistband', 'Drawstring', 'Ribbed ankle cuffs'],
                    'available_sizes' => ['S', 'M', 'L', 'XL'],
                    'care_instructions' => ['Machine wash cold', 'Wash with similar colors'],
                ],
            ],
            [
                'name' => 'V-Neck T-Shirt',
                'description' => 'A lightweight v-neck t-shirt with a simple modern style.',
                'price' => 135000,
                'stock' => 60,
                'image_path' => 'products/clothing-1.webp',
                'specifications' => [
                    'material' => 'Cotton',
                    'color' => 'Charcoal gray',
                    'fit' => 'Regular fit',
                    'style' => 'Minimalist casual',
                    'intended_use' => ['Everyday wear', 'Layering'],
                    'features' => ['V-neck', 'Short sleeves', 'Plain design'],
                    'available_sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'care_instructions' => ['Machine wash cold', 'Wash with similar colors'],
                ],
            ],
            [
                'name' => 'Oxford Shirt',
                'description' => 'A versatile oxford shirt suitable for casual and smart outfits.',
                'price' => 265000,
                'stock' => 25,
                'image_path' => 'products/clothing-2.webp',
                'specifications' => [
                    'material' => 'Oxford cotton fabric',
                    'color' => 'Light blue',
                    'fit' => 'Regular fit',
                    'style' => 'Smart casual',
                    'intended_use' => ['Casual office', 'Smart casual events', 'Everyday wear'],
                    'features' => ['Button-down collar', 'Button-front closure', 'Long sleeves'],
                    'available_sizes' => ['S', 'M', 'L', 'XL'],
                    'care_instructions' => ['Machine wash cold', 'Iron at moderate temperature'],
                ],
            ],
            [
                'name' => 'Bomber Jacket',
                'description' => 'A lightweight bomber jacket with a modern casual look.',
                'price' => 390000,
                'stock' => 22,
                'image_path' => 'products/clothing-3.webp',
                'specifications' => [
                    'material' => 'Polyester',
                    'color' => 'Black',
                    'fit' => 'Regular fit',
                    'style' => 'Modern streetwear',
                    'intended_use' => ['Everyday wear', 'Casual outings', 'Layering'],
                    'features' => ['Zip-front closure', 'Ribbed cuffs', 'Ribbed hem', 'Side pockets'],
                    'available_sizes' => ['S', 'M', 'L', 'XL'],
                    'care_instructions' => ['Machine wash cold on gentle cycle', 'Air dry'],
                ],
            ],
            [
                'name' => 'Windbreaker Jacket',
                'description' => 'A lightweight jacket designed to provide protection from wind.',
                'price' => 365000,
                'stock' => 27,
                'image_path' => 'products/clothing-4.webp',
                'specifications' => [
                    'material' => 'Polyester',
                    'color' => 'Navy blue',
                    'fit' => 'Regular fit',
                    'style' => 'Outdoor casual',
                    'intended_use' => ['Windy conditions', 'Outdoor activities', 'Layering'],
                    'features' => ['Lightweight construction', 'Zip-front closure', 'Side pockets'],
                    'available_sizes' => ['S', 'M', 'L', 'XL'],
                    'care_instructions' => ['Machine wash cold', 'Air dry'],
                ],
            ],
            [
                'name' => 'Chino Pants',
                'description' => 'Classic chino pants with a clean and comfortable fit.',
                'price' => 285000,
                'stock' => 33,
                'image_path' => 'products/clothing-5.webp',
                'specifications' => [
                    'material' => 'Cotton blend',
                    'color' => 'Beige',
                    'fit' => 'Regular fit',
                    'style' => 'Smart casual',
                    'intended_use' => ['Everyday wear', 'Casual office', 'Smart casual outfits'],
                    'features' => ['Belt loops', 'Front and back pockets', 'Button and zipper closure'],
                    'available_sizes' => ['28', '30', '32', '34', '36'],
                    'care_instructions' => ['Machine wash cold', 'Wash with similar colors'],
                ],
            ],
            [
                'name' => 'Linen Shirt',
                'description' => 'A lightweight linen shirt designed for warm weather.',
                'price' => 230000,
                'stock' => 42,
                'image_path' => 'products/clothing-1.webp',
                'specifications' => [
                    'material' => 'Linen',
                    'color' => 'Natural beige',
                    'fit' => 'Regular fit',
                    'style' => 'Relaxed smart casual',
                    'intended_use' => ['Warm weather', 'Holiday wear', 'Casual outings'],
                    'features' => ['Lightweight fabric', 'Button-front closure', 'Long sleeves'],
                    'available_sizes' => ['S', 'M', 'L', 'XL'],
                    'care_instructions' => ['Gentle wash', 'Air dry', 'Iron at low to moderate temperature'],
                ],
            ],
            [
                'name' => 'Training Jacket',
                'description' => 'A lightweight training jacket designed for sports activities.',
                'price' => 315000,
                'stock' => 31,
                'image_path' => 'products/clothing-2.webp',
                'specifications' => [
                    'material' => 'Polyester',
                    'color' => 'Black',
                    'fit' => 'Athletic fit',
                    'style' => 'Sportswear',
                    'intended_use' => ['Warm-ups', 'Outdoor training', 'Sports activities'],
                    'features' => ['Zip-front closure', 'Long sleeves', 'Side pockets'],
                    'available_sizes' => ['S', 'M', 'L', 'XL'],
                    'care_instructions' => ['Machine wash cold', 'Air dry'],
                ],
            ],
            [
                'name' => 'Graphic T-Shirt',
                'description' => 'A casual graphic t-shirt with a modern printed design.',
                'price' => 155000,
                'stock' => 48,
                'image_path' => 'products/clothing-3.webp',
                'specifications' => [
                    'material' => 'Cotton',
                    'color' => 'Black',
                    'fit' => 'Regular fit',
                    'style' => 'Graphic streetwear',
                    'intended_use' => ['Everyday wear', 'Casual outings', 'Streetwear outfits'],
                    'features' => ['Printed graphic', 'Short sleeves', 'Crew neck'],
                    'available_sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'care_instructions' => ['Machine wash cold', 'Turn inside out before washing'],
                ],
            ],
            [
                'name' => 'Winter Hoodie',
                'description' => 'A warm hoodie designed for comfortable cold-weather wear.',
                'price' => 320000,
                'stock' => 20,
                'image_path' => 'products/clothing-4.webp',
                'specifications' => [
                    'material' => 'Cotton-polyester fleece blend',
                    'color' => 'Dark gray',
                    'fit' => 'Relaxed fit',
                    'style' => 'Cold-weather casual',
                    'intended_use' => ['Cool weather', 'Layering', 'Everyday wear'],
                    'features' => ['Hood', 'Front pocket', 'Long sleeves', 'Fleece-style interior'],
                    'available_sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'care_instructions' => ['Machine wash cold', 'Turn inside out before washing'],
                ],
            ],
            [
                'name' => 'Relax Fit T-Shirt',
                'description' => 'A relaxed fit t-shirt with a comfortable everyday silhouette.',
                'price' => 140000,
                'stock' => 52,
                'image_path' => 'products/clothing-5.webp',
                'specifications' => [
                    'material' => 'Cotton',
                    'color' => 'Cream',
                    'fit' => 'Relaxed fit',
                    'style' => 'Minimalist casual',
                    'intended_use' => ['Everyday wear', 'Relaxed outfits', 'Layering'],
                    'features' => ['Roomier silhouette', 'Short sleeves', 'Crew neck'],
                    'available_sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'care_instructions' => ['Machine wash cold', 'Wash with similar colors'],
                ],
            ],
        ];
    }

    /**
     * Electronics products.
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
                'specifications' => [
                    'device_type' => 'Keyboard',
                    'connectivity' => ['USB wired'],
                    'compatibility' => ['Desktop computers', 'Laptops with a compatible USB port'],
                    'features' => ['Mechanical keys', 'Dedicated number pad', 'Adjustable typing angle'],
                    'intended_use' => ['Office work', 'Programming', 'Gaming'],
                ],
            ],
            [
                'name' => 'Wireless Mouse',
                'description' => 'A compact wireless mouse designed for everyday productivity.',
                'price' => 350000,
                'stock' => 40,
                'image_path' => 'products/electronics-2.webp',
                'specifications' => [
                    'device_type' => 'Computer mouse',
                    'connectivity' => ['2.4 GHz wireless USB receiver'],
                    'compatibility' => ['Desktop computers', 'Laptops with a compatible USB port'],
                    'features' => ['Optical tracking', 'Scroll wheel', 'Compact design'],
                    'intended_use' => ['Office work', 'Study', 'Everyday computer use'],
                ],
            ],
            [
                'name' => 'Bluetooth Speaker',
                'description' => 'A portable Bluetooth speaker with clear and balanced sound.',
                'price' => 650000,
                'stock' => 30,
                'image_path' => 'products/electronics-3.webp',
                'specifications' => [
                    'device_type' => 'Portable speaker',
                    'connectivity' => ['Bluetooth'],
                    'compatibility' => ['Bluetooth-enabled phones', 'Tablets', 'Laptops'],
                    'features' => ['Portable design', 'Wireless audio playback', 'Built-in controls'],
                    'intended_use' => ['Home listening', 'Travel', 'Small gatherings'],
                ],
            ],
            [
                'name' => 'Gaming Headset',
                'description' => 'A gaming headset designed for immersive audio and communication.',
                'price' => 950000,
                'stock' => 18,
                'image_path' => 'products/electronics-4.webp',
                'specifications' => [
                    'device_type' => 'Headset',
                    'connectivity' => ['3.5 mm audio jack'],
                    'compatibility' => ['Compatible computers', 'Compatible gaming consoles with a 3.5 mm audio jack'],
                    'features' => ['Built-in microphone', 'Over-ear earcups', 'In-line audio controls'],
                    'intended_use' => ['Gaming', 'Voice chat', 'Online meetings'],
                ],
            ],
            [
                'name' => 'USB-C Hub',
                'description' => 'A versatile USB-C hub for connecting multiple peripheral devices.',
                'price' => 450000,
                'stock' => 35,
                'image_path' => 'products/electronics-5.webp',
                'specifications' => [
                    'device_type' => 'Connectivity hub',
                    'connectivity' => ['USB-C host connection'],
                    'compatibility' => ['Devices with a compatible USB-C port'],
                    'features' => ['Multiple peripheral connections', 'Compact design', 'Plug-and-play where supported'],
                    'intended_use' => ['Laptop workstations', 'Connecting peripherals', 'Study and office work'],
                    'notes' => ['Exact port types and supported power delivery should be verified against the product listing.'],
                ],
            ],
            [
                'name' => '27 Inch Monitor',
                'description' => 'A 27-inch monitor suitable for productivity and entertainment.',
                'price' => 2850000,
                'stock' => 12,
                'image_path' => 'products/electronics-1.webp',
                'specifications' => [
                    'device_type' => 'Monitor',
                    'screen_size' => '27 inches',
                    'connectivity' => ['Display connection; exact ports to be confirmed'],
                    'compatibility' => ['Computers with a compatible display output'],
                    'features' => ['Large display area', 'Suitable for desk setups'],
                    'intended_use' => ['Productivity', 'Study', 'Entertainment'],
                    'notes' => ['Resolution and refresh rate have not been specified in this demo dataset.'],
                ],
            ],
            [
                'name' => 'Portable SSD',
                'description' => 'A compact portable SSD for fast and reliable data storage.',
                'price' => 1250000,
                'stock' => 20,
                'image_path' => 'products/electronics-2.webp',
                'specifications' => [
                    'device_type' => 'External solid-state drive',
                    'connectivity' => ['USB connection; exact connector to be confirmed'],
                    'compatibility' => ['Computers with a compatible USB port'],
                    'features' => ['Portable storage', 'Compact design', 'Solid-state storage'],
                    'intended_use' => ['File backups', 'Moving files', 'Additional computer storage'],
                    'notes' => ['Storage capacity and transfer speed have not been specified in this demo dataset.'],
                ],
            ],
            [
                'name' => 'Wireless Charger',
                'description' => 'A convenient wireless charger for compatible smartphones and devices.',
                'price' => 325000,
                'stock' => 45,
                'image_path' => 'products/electronics-3.webp',
                'specifications' => [
                    'device_type' => 'Wireless charging accessory',
                    'connectivity' => ['Wireless charging surface', 'Power input; connector to be confirmed'],
                    'compatibility' => ["Devices supporting the charger wireless charging standard"],
                    'features' => ['Cable-free device charging', 'Desk-friendly design'],
                    'intended_use' => ['Desk charging', 'Everyday phone charging'],
                    'notes' => ['Charging wattage and supported standards have not been specified in this demo dataset.'],
                ],
            ],
            [
                'name' => 'Power Bank 20000mAh',
                'description' => 'A high-capacity power bank designed for charging devices on the go.',
                'price' => 550000,
                'stock' => 28,
                'image_path' => 'products/electronics-4.webp',
                'specifications' => [
                    'device_type' => 'Portable power bank',
                    'capacity' => '20000 mAh',
                    'connectivity' => ['USB output; exact port types to be confirmed'],
                    'compatibility' => ['Devices chargeable through a compatible USB connection'],
                    'features' => ['Portable battery capacity', 'Designed for charging on the go'],
                    'intended_use' => ['Travel', 'Commuting', 'Backup device charging'],
                    'notes' => ['Charging speed and number of ports have not been specified in this demo dataset.'],
                ],
            ],
            [
                'name' => 'Smart Watch',
                'description' => 'A modern smartwatch with useful everyday tracking features.',
                'price' => 1450000,
                'stock' => 15,
                'image_path' => 'products/electronics-5.webp',
                'specifications' => [
                    'device_type' => 'Smartwatch',
                    'connectivity' => ['Bluetooth'],
                    'compatibility' => ['Compatible smartphones'],
                    'features' => ['Activity tracking', 'Everyday notifications', 'Watch-face display'],
                    'intended_use' => ['Everyday timekeeping', 'Activity tracking', 'Viewing notifications'],
                    'notes' => ['Supported phone operating systems, water resistance, and battery life have not been specified in this demo dataset.'],
                ],
            ],
        ];
    }

    /**
     * Accessories products.
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
                'specifications' => [
                    'accessory_type' => 'Wallet',
                    'material' => 'Leather',
                    'color' => 'Brown',
                    'features' => ['Compact form', 'Card slots', 'Cash compartment'],
                    'intended_use' => ['Everyday carry', 'Organizing cards and cash'],
                ],
            ],
            [
                'name' => 'Canvas Backpack',
                'description' => 'A durable canvas backpack suitable for everyday activities.',
                'price' => 325000,
                'stock' => 30,
                'image_path' => 'products/accessories-2.webp',
                'specifications' => [
                    'accessory_type' => 'Backpack',
                    'material' => 'Canvas',
                    'color' => 'Beige',
                    'features' => ['Shoulder straps', 'Main compartment', 'Additional storage pocket'],
                    'intended_use' => ['Everyday carry', 'Study', 'Commuting'],
                    'notes' => ['Capacity and dimensions have not been specified in this demo dataset.'],
                ],
            ],
            [
                'name' => 'Baseball Cap',
                'description' => 'A casual baseball cap designed for everyday outdoor use.',
                'price' => 95000,
                'stock' => 55,
                'image_path' => 'products/accessories-3.webp',
                'specifications' => [
                    'accessory_type' => 'Cap',
                    'material' => 'Cotton twill',
                    'color' => 'Black',
                    'features' => ['Curved brim', 'Adjustable back strap'],
                    'intended_use' => ['Everyday wear', 'Outdoor casual activities'],
                ],
            ],
            [
                'name' => 'Travel Pouch',
                'description' => 'A compact travel pouch for organizing small personal items.',
                'price' => 125000,
                'stock' => 45,
                'image_path' => 'products/accessories-4.webp',
                'specifications' => [
                    'accessory_type' => 'Organizer pouch',
                    'material' => 'Nylon',
                    'color' => 'Navy blue',
                    'features' => ['Zip closure', 'Compact storage compartment'],
                    'intended_use' => ['Travel organization', 'Storing small personal items'],
                ],
            ],
            [
                'name' => 'Phone Stand',
                'description' => 'A simple phone stand for convenient viewing on a desk.',
                'price' => 75000,
                'stock' => 60,
                'image_path' => 'products/accessories-5.webp',
                'specifications' => [
                    'accessory_type' => 'Phone stand',
                    'material' => 'ABS plastic',
                    'color' => 'Black',
                    'compatibility' => ['Phones that fit the stand dimensions'],
                    'features' => ['Hands-free viewing', 'Desk placement', 'Compact design'],
                    'intended_use' => ['Video calls', 'Watching videos', 'Desk use'],
                    'notes' => ['Supported device dimensions and angle adjustment have not been specified in this demo dataset.'],
                ],
            ],
        ];
    }
}