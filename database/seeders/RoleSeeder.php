<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(['name' => 'admin']);
        $collaborator = Role::create(['name' => 'collaborator']);
        $client = Role::create(['name' => 'client']);

        // Permission::create(['name' => 'email.all', 'guard_name' => 'web'])->syncRoles([$admin, $collaborator]);
    }
}
