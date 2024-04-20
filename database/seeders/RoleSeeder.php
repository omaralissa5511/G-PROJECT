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
        $super_Admin = Role::create(['name' => 'Super Admin', 'guard_name' => 'spatie']);
        $admin = Role::create(['name' => 'Admin']);
        $productManager = Role::create(['name' => 'Normal User']);

        $super_Admin ->givePermissionTo([
            'create-role',
            'edit-role',
            'delete-role',
            'create-user',
            'edit-user',
            'delete-user',
            'create-product',
            'edit-product',
            'delete-product']);

        $admin->givePermissionTo([
            'create-user',
            'edit-user',
            'delete-user',
            'create-product',
            'edit-product',
            'delete-product'
        ]);

        $productManager->givePermissionTo([
            'create-product',
            'edit-product',
            'delete-product'
        ]);
    }
}
