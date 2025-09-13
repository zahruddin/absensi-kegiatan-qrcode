<?php

namespace App\Exports;

use App\Models\Peserta;
use App\Models\Kegiatan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PesertaExport implements FromQuery, WithHeadings, WithMapping
{
    protected $kegiatan;

    public function __construct(Kegiatan $kegiatan)
    {
        $this->kegiatan = $kegiatan;
    }

    /**
     * Menentukan query Eloquent untuk mengambil data peserta dari database.
     * Menggunakan FromQuery lebih efisien untuk data besar.
     */
    public function query()
    {
        return Peserta::query()->where('id_kegiatan', $this->kegiatan->id);
    }

    /**
     * Menentukan header atau judul untuk setiap kolom di file Excel.
     */
    public function headings(): array
    {
        return [
            'nama',
            'nim',
            'email',
            'no_hp',
            'prodi',
            'kelompok',
            'token',
            'qrcode',
        ];
    }

    /**
     * Memetakan data dari setiap model Peserta ke dalam baris Excel.
     * Ini memastikan urutan data sesuai dengan header.
     * @param Peserta $peserta
     */
    public function map($peserta): array
    {
        return [
            $peserta->nama,
            $peserta->nim,
            $peserta->email,
            $peserta->no_hp,
            $peserta->prodi,
            $peserta->kelompok,
            $peserta->token,
            $peserta->qrcode,
        ];
    }
}