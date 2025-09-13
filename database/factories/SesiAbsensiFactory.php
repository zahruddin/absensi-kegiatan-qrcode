<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SesiAbsensiFactory extends Factory
{
    public function definition(): array
    {
        return [
            // id_kegiatan akan di-set dari Seeder
            'nama' => fake()->randomElement(['Absen Pagi', 'Sesi 1', 'Sesi 2', 'Absen Sore']),
        ];
    }
}