<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Membuat 1 user Admin
        User::create([
            'name' => 'Admin Utama',
            'username' => 'admin',
            'email' => 'admin@contoh.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // 2. Membuat 5 user Operator menggunakan factory
        User::factory(1)->create([
            'role' => 'operator',
            'password' => Hash::make('admin123'),

        ]);

        // 3. Membuat 10 user Peserta menggunakan factory
        // Sebenarnya user peserta lebih baik dibuat bersamaan dengan data peserta di kegiatan,
        // namun untuk contoh, kita buat di sini.
        // User::factory(10)->create([
        //     'role' => 'peserta',
        // ]);
    }
}