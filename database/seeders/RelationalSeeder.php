<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Kegiatan;
use App\Models\SesiAbsensi;
use App\Models\Peserta;
use Illuminate\Database\Seeder;

class RelationalSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua kegiatan yang ada
        $kegiatanList = Kegiatan::all();

        // Iterasi untuk setiap kegiatan
        foreach ($kegiatanList as $kegiatan) {
            
            // 1. Buat 50 peserta untuk setiap kegiatan
            $pesertaList = Peserta::factory(50)->create([
                'id_kegiatan' => $kegiatan->id,
            ]);

            // 2. Buat 3 kategori absensi untuk setiap kegiatan
            $kategoriList = collect(); // Gunakan collection untuk menampung
            $kategoriNama = ['Absen Masuk', 'Absen Sesi Siang', 'Absen Pulang'];
            foreach ($kategoriNama as $nama) {
                $kategoriList->push(
                    SesiAbsensi::factory()->create([
                        'id_kegiatan' => $kegiatan->id,
                        'nama' => $nama,
                    ])
                );
            }

            // 3. Buat data absensi untuk setiap peserta di kegiatan ini
            foreach ($pesertaList as $peserta) {
                // Setiap peserta melakukan absen untuk 1 sampai 3 kategori secara acak
                $jumlahAbsen = rand(1, 3);
                $kategoriDipilih = $kategoriList->random($jumlahAbsen);

                foreach ($kategoriDipilih as $kategori) {
                    Absensi::factory()->create([
                        'id_peserta' => $peserta->id,
                        'id_sesi' => $kategori->id,
                    ]);
                }
            }
        }
    }
}