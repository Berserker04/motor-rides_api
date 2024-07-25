<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Position::create(['name' => 'Gerente', 'positionState_id' => 1]);
        Position::create(['name' => 'Contadora', 'positionState_id' => 1]);
        Position::create(['name' => 'Talendo humano', 'positionState_id' => 1]);
    }
}
