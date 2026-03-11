<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'admin', 'guard_name' => 'admin']);
        Role::create(['name' => 'technical', 'guard_name' => 'admin']);
        Role::create(['name' => 'super_admin', 'guard_name' => 'admin']);
        Role::create(['name' => 'delegate','guard_name' => 'admin']);
        Role::create(['name' => 'accountant','guard_name' => 'admin']);
    }
}
