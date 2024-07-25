<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(EmailStateSeeder::class);
        $this->call(UserStateSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(NewsStateSeeder::class);
        $this->call(PositionSeeder::class);
        $this->call(PositionStateSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(SubCategorySeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(ProductStateSeeder::class);
    }
}
