<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableSeeder extends Seeder
{
    public function run()
    {
        $jumlahMeja = 10; // Atur sesuai kebutuhan
        $baseUrl = config('app.url');

        for ($i = 1; $i <= $jumlahMeja; $i++) {
            $namaMeja = 'Meja ' . $i;

            // Simpan dulu datanya ke DB agar dapat ID
            $id = DB::table('tables')->insertGetId([
                'nama_meja' => $namaMeja,
                'qr_code' => '', // Akan diupdate setelah QR dibuat
                'status' => 'ready',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Buat konten dan nama file QR code
            $qrContent = $baseUrl . '/scan/' . $id;
            $filename = 'qr_meja_' . $id . '_' . time() . '.png';
            $storagePath = storage_path('app/public/qrcode');

            // Pastikan folder tujuan ada
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            // Generate dan simpan QR code
            $qrCode = QrCode::format('png')
                            ->size(300)
                            ->margin(2)
                            ->generate($qrContent);

            file_put_contents($storagePath . '/' . $filename, $qrCode);

            // Update tabel dengan path QR code
            DB::table('tables')->where('id', $id)->update([
                'qr_code' => 'qrcode/' . $filename
            ]);
        }
    }
}
