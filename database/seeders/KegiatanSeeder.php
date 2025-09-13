<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kegiatan;

class KegiatanSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat 3 data kegiatan random
        Kegiatan::factory(3)->create();
    }
}