<?php

namespace Database\Seeders;

use App\Models\EmailState;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EmailState::create(['name' => 'leer']);
        EmailState::create(['name' => 'leido']);
        EmailState::create(['name' => 'eliminado']);
    }
}
