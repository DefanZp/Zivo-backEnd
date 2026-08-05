<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create([
            'name' => 'Clothing',
            'slug' => 'clothing',
        ]);

        Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        Category::create([
            'name' => 'Accessories',
            'slug' => 'accessories',
        ]);
    }
}