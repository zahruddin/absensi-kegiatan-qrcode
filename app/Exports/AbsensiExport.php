<?php

namespace App\Exports;

use App\Models\Absensi;
use App\Models\Kegiatan;
use App\Models\SesiAbsensi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AbsensiExport implements FromQuery, WithHeadings, WithMapping
{
    protected $kegiatan;
    protected $id_sesi;

    public function __construct(Kegiatan $kegiatan, $id_sesi)
    {
        $this->kegiatan = $kegiatan;
        $this->id_sesi = $id_sesi;
    }

    public function query()
    {
        $query = Absensi::query()
            ->with(['peserta', 'sesi']) // Eager load relasi untuk efisiensi
            ->orderBy('waktu_absen', 'asc');

        if ($this->id_sesi === 'all') {
            // Jika pilih "semua", ambil ID semua sesi dari kegiatan ini
            $sesiIds = SesiAbsensi::where('id_kegiatan', $this->kegiatan->id)->pluck('id');
            $query->whereIn('id_sesi', $sesiIds);
        } else {
            // Jika pilih sesi spesifik
            $query->where('id_sesi', $this->id_sesi);
        }
        
        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Peserta',
            'NIM',
            'Program Studi',
            'Sesi Absensi',
            'Waktu Absen',
        ];
    }

    public function map($absensi): array
    {
        static $index = 0;
        $index++;

        // ✅ KODE YANG LEBIH AMAN
        return [
            $index,
            $absensi->peserta->nama ?? 'PESERTA DIHAPUS', // Jika peserta null, tampilkan teks ini
            $absensi->peserta->nim ?? 'N/A',
            $absensi->peserta->prodi ?? 'N/A',
            $absensi->sesi->nama ?? 'SESI DIHAPUS', // Jika sesi null, tampilkan teks ini
            $absensi->waktu_absen->format('d M Y, H:i:s'),
        ];
    }
}