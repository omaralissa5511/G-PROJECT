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
        $super_Admin = Role::create(['name' => 'ADMIN', 'guard_name' => 'spatie']);
        $club = Role::create(['name' => 'CLUB']);
        $health = Role::create(['name' => 'HEALTH']);
        $trainer = Role::create(['name' => 'TRAINER']);
        $SB = Role::create(['name' => 'SB']);
        $user = Role::create(['name' => 'USER']);
    }
}
