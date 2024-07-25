<?php

namespace Database\Seeders;

use App\Models\SubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubCategory::create([
            'name' => 'sport',
            'slug' => Str::slug('sport'),
            'isDeleted' => "N",
            'category_id' => 1
        ]);

        SubCategory::create([
            'name' => 'rines',
            'slug' => Str::slug('rines'),
            'isDeleted' => "N",
            'category_id' => 2
        ]);

        SubCategory::create([
            'name' => 'cilindros',
            'slug' => Str::slug('cilindros'),
            'isDeleted' => "N",
            'category_id' => 3
        ]);
        SubCategory::create([
            'name' => 'bateria para moto',
            'slug' => Str::slug('bateria para moto'),
            'isDeleted' => "N",
            'category_id' => 4
        ]);

        SubCategory::create([
            'name' => 'motos',
            'slug' => Str::slug('motos'),
            'isDeleted' => "N",
            'category_id' => 5
        ]);

        SubCategory::create([
            'name' => 'cascos',
            'slug' => Str::slug('cascos'),
            'isDeleted' => "N",
            'category_id' => 6
        ]);
    }
}
