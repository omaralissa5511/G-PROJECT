<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creating Super Admin User
        $superAdmin = User::create([
            'name' => 'Cristiano Ronaldo',
            'email' => 'Cristiano@gmail.com',
            'password' => Hash::make('00000000')
        ]);
        $superAdmin->assignRole('Super Admin');

        // Creating Admin User
        $admin = User::create([
            'name' => 'Karim Benzema',
            'email' => 'Karim@gmail.com',
            'password' => Hash::make('00000000')
        ]);
        $admin->assignRole('Admin');

        // Creating Product Manager User
        $productManager = User::create([
            'name' => 'Lewandowski',
            'email' => 'Lewandowski@allphptricks.com',
            'password' => Hash::make('00000000')
        ]);
        $productManager->assignRole('Normal User');
    }
}
