<?php

namespace Database\Seeders;

use App\Models\PositionState;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PositionStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PositionState::create(['name' => 'activo']);
        PositionState::create(['name' => 'eliminado']);
    }
}
