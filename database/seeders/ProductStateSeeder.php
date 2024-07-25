<?php

namespace Database\Seeders;

use App\Models\ProductState;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductState::create(['name' => 'activo']);
        ProductState::create(['name' => 'no disponible']);
        ProductState::create(['name' => 'eliminado']);
    }
}
