<?php

namespace App\Imports;

use App\Models\Peserta;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage; // <-- Pastikan Storage di-import
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PesertaImport implements ToCollection, WithHeadingRow
{
    private $id_kegiatan;

    public function __construct(int $id_kegiatan)
    {
        $this->id_kegiatan = $id_kegiatan;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            if (empty($row['nama'])) {
                continue;
            }

            // KONDISI 1: JIKA TOKEN SUDAH ADA (UPDATE DATA)
            if (!empty($row['token'])) {
                
                // ✅ LOGIKA VERIFIKASI QR CODE BARU DIMULAI DI SINI
                $qrCodePath = $row['qrcode'] ?? null;

                // Cek jika path QR kosong ATAU jika file-nya tidak ada di storage
                if (empty($qrCodePath) || !Storage::disk('public')->exists($qrCodePath)) {
                    
                    // Jika tidak ada, generate ulang QR Code berdasarkan token yang ada
                    $safeName = Str::slug($row['nama']);
                    $fileName = 'qrcode_' . $safeName . '_' . time() . '.png';
                    $newPath = 'qrcode_peserta/' . $fileName;

                    $qrCodeImage = QrCode::format('png')->size(300)->margin(2)->generate($row['token']);
                    Storage::disk('public')->put($newPath, $qrCodeImage);
                    
                    // Gunakan path yang baru untuk disimpan ke database
                    $qrCodePath = $newPath;
                }
                // ✅ AKHIR LOGIKA VERIFIKASI QR CODE

                // Lanjutkan proses update/create dengan path QR yang sudah diverifikasi/dibuat ulang
                Peserta::updateOrCreate(
                    [
                        'token' => $row['token'],
                        'id_kegiatan' => $this->id_kegiatan,
                    ],
                    [
                        'nama'     => $row['nama'],
                        'nim'      => $row['nim'] ?? null,
                        'email'    => $row['email'] ?? null,
                        'no_hp'    => $row['no_hp'] ?? null,
                        'prodi'    => $row['prodi'] ?? null,
                        'kelompok' => $row['kelompok'] ?? null,
                        'qrcode'   => $qrCodePath, // <-- Gunakan path yang sudah final
                    ]
                );

            } else {
                // KONDISI 2: JIKA TOKEN TIDAK ADA (BUAT PESERTA BARU)
                // Logika ini tidak berubah
                $uniqueToken = Str::random(40);
                $safeName = Str::slug($row['nama']);
                $fileName = 'qrcode_' . $safeName . '_' . time() . '.png';
                $filePath = 'qrcode_peserta/' . $fileName;

                $qrCodeImage = QrCode::format('png')->size(300)->margin(2)->generate($uniqueToken);
                Storage::disk('public')->put($filePath, $qrCodeImage);
                
                Peserta::create([
                    'id_kegiatan' => $this->id_kegiatan,
                    'nama'        => $row['nama'],
                    'nim'         => $row['nim'] ?? null,
                    'email'       => $row['email'] ?? null,
                    'no_hp'       => $row['no_hp'] ?? null,
                    'prodi'       => $row['prodi'] ?? null,
                    'kelompok'    => $row['kelompok'] ?? null,
                    'qrcode'      => $filePath,
                    'token'       => $uniqueToken,
                ]);
            }
        }
    }
}