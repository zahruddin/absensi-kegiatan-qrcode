<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PesertaFactory extends Factory
{
    public function definition(): array
    {
        return [
            // id_kegiatan akan di-set dari Seeder
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'no_hp' => fake()->phoneNumber(),
            'prodi' => fake()->randomElement(['Teknik Informatika', 'Sistem Informasi', 'Manajemen', 'Akuntansi']),
            'nim' => '2215' . fake()->unique()->numerify('######'),
            'kelompok' => fake()->numberBetween(1, 10),
            'qrcode' => Str::random(15),
            'token' => Str::random(40),
        ];
    }
}