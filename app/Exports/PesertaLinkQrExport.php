<?php

namespace App\Exports;

use App\Models\Kegiatan;
use App\Models\Peserta;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PesertaLinkQrExport implements FromQuery, WithHeadings, WithMapping
{
    protected $kegiatan;

    public function __construct(Kegiatan $kegiatan)
    {
        $this->kegiatan = $kegiatan;
    }

    /**
     * Menentukan query untuk mengambil data peserta dari database.
     */
    public function query()
    {
        return Peserta::query()->where('id_kegiatan', $this->kegiatan->id);
    }

    /**
     * Menentukan header untuk setiap kolom di file Excel.
     */
    public function headings(): array
    {
        return [
            'nama',
            'nim',
            'email', // <-- Kolom email ditambahkan
            'link_qrcode',
        ];
    }

    /**
     * Memetakan data dari setiap model Peserta ke dalam baris Excel.
     * @param Peserta $peserta
     */
    public function map($peserta): array
    {
        if ($peserta->qrcode) {
            $link = asset('storage/' . $peserta->qrcode);
        } else {
            $link = 'QR Code belum di-generate';
        }

        return [
            $peserta->nama,
            $peserta->nim,
            $peserta->email, // <-- Data email ditambahkan di sini
            $link,
        ];
    }
}