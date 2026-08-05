<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ECommerceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clothing = Category::create([
            'name' => 'Clothing',
            'slug' => 'clothing',
        ]);

        $electronics = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        Product::create([
            'category_id' => $clothing->id,
            'name' => 'Classic White T-Shirt',
            'description' => 'Kaos polos putih katun berkualitas tinggi, nyaman dipakai sehari-hari.',
            'price' => 120000,
            'stock' => 50,
            'image_path' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=500'
        ]);

        Product::create([
            'category_id' => $clothing->id,
            'name' => 'Denim Jacket Blue',
            'description' => 'Jaket denim kasual berwarna biru pudar untuk penampilan kasual.',
            'price' => 350000,
            'stock' => 20,
            'image_path' => 'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?w=500'
        ]);

         Product::create([
            'category_id' => $electronics->id,
            'name' => 'Wireless Headphones',
            'description' => 'Headphone nirkabel dengan fitur peredam bising aktif dan suara bass yang jernih.',
            'price' => 850000,
            'stock' => 15,
            'image_path' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500'
        ]);
    }
}
