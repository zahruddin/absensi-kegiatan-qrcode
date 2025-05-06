<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Membuat user dengan role admin
        User::create([
            'name' => 'Admin User',
            'username' => 'admin123',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Membuat user dengan role kasir
        User::create([
            'name' => 'Kasir User',
            'username' => 'kasir123',
            'email' => 'kasir@example.com',
            'password' => Hash::make('password123'),
            'role' => 'kasir',
        ]);

        // Membuat user dengan role customer
        User::create([
            'name' => 'Customer User',
            'username' => 'customer123',
            'email' => 'customer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);
    }
}
