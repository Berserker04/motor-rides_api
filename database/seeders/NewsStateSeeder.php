<?php

namespace Database\Seeders;

use App\Models\NewsState;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NewsState::create(['name' => 'activo']);
        NewsState::create(['name' => 'eliminado']);
    }
}
