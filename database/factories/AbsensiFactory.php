<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AbsensiFactory extends Factory
{
    public function definition(): array
    {
        return [
            // id_peserta dan id_kategori akan di-set dari Seeder
            'datetime' => fake()->dateTimeBetween('-1 week', 'now'),
        ];
    }
}