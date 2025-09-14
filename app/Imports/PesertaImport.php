<?php

namespace App\Imports;

use App\Models\Peserta;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
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
                
                $qrCodePath = $row['qrcode'] ?? null;

                // Cek jika path QR kosong ATAU jika file-nya tidak ada di storage
                if (empty($qrCodePath) || !Storage::disk('public')->exists($qrCodePath)) {
                    
                    $safeName = Str::slug($row['nama']);
                    // ✅ DIUBAH: Nama file sekarang konsisten, menggunakan bagian dari token
                    $uniquePart = substr($row['token'], 0, 8); // Ambil 8 karakter pertama token
                    $fileName = 'qrcode_' . $safeName . '_' . $uniquePart . '.png';
                    $newPath = 'qrcode_peserta/' . $fileName;

                    $qrCodeImage = QrCode::format('png')->size(300)->margin(2)->generate($row['token']);
                    Storage::disk('public')->put($newPath, $qrCodeImage);
                    
                    $qrCodePath = $newPath;
                }
                
                Peserta::updateOrCreate(
                    ['token' => $row['token'], 'id_kegiatan' => $this->id_kegiatan],
                    [
                        'nama'     => $row['nama'],
                        'nim'      => $row['nim'] ?? null,
                        'email'    => $row['email'] ?? null,
                        'no_hp'    => $row['no_hp'] ?? null,
                        'prodi'    => $row['prodi'] ?? null,
                        'kelompok' => $row['kelompok'] ?? null,
                        'qrcode'   => $qrCodePath,
                    ]
                );

            } else {
                // KONDISI 2: JIKA TOKEN TIDAK ADA (BUAT PESERTA BARU)
                $uniqueToken = Str::random(40);
                $safeName = Str::slug($row['nama']);
                
                // ✅ DIUBAH: Gunakan logika penamaan yang sama agar konsisten
                $uniquePart = substr($uniqueToken, 0, 8);
                $fileName = 'qrcode_' . $safeName . '_' . $uniquePart . '.png';
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