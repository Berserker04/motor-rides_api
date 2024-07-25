<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Llantas',
            'slug' => Str::slug('Llantas'),
            'isDeleted' => "N",
        ]);

        Category::create([
            'name' => 'transmisión',
            'slug' => Str::slug('transmisión'),
            'isDeleted' => "N",
        ]);

        Category::create([
            'name' => 'respuestos',
            'slug' => Str::slug('respuestos'),
            'isDeleted' => "N",
        ]);
        Category::create([
            'name' => 'electronicos',
            'slug' => Str::slug('electronicos'),
            'isDeleted' => "N",
        ]);

        Category::create([
            'name' => 'lubricantes',
            'slug' => Str::slug('lubricantes'),
            'isDeleted' => "N",
        ]);

        Category::create([
            'name' => 'accesorios',
            'slug' => Str::slug('accesorios'),
            'isDeleted' => "N",
        ]);
    }
}
