<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'title' => 'Llanta deportiva',
            'slug' => Str::slug('Llanta deportiva'),
            'description' => "test",
            'image' => "test.png",
            'price' => 60000,
            'sub_category_id' => 1,
            'productState_id' => 1,
        ]);
    }
}
