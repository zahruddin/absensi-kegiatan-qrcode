<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str; // Tambahkan ini jika belum ada

class KegiatanFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Mengambil id_user secara acak dari user yang BUKAN 'peserta'
            'id_user' => User::where('role', '!=', 'peserta')->inRandomOrder()->first()->id,
            'nama' => 'Kegiatan ' . fake()->words(3, true),
            'qrcode' => Str::random(10),
            'date' => fake()->date(),
        ];
    }
}