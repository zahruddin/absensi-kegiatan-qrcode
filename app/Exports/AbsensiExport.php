<?php

namespace App\Exports;

use App\Models\Absensi;
use App\Models\Kegiatan;
use App\Models\SesiAbsensi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AbsensiExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected Kegiatan $kegiatan;
    protected $id_sesi;
    protected Collection $sesiUntukExport;

    public function __construct(Kegiatan $kegiatan, $id_sesi)
    {
        $this->kegiatan = $kegiatan;
        $this->id_sesi = $id_sesi;

        // Tentukan sesi mana yang akan diekspor berdasarkan pilihan operator
        if ($this->id_sesi === 'all') {
            $this->sesiUntukExport = $this->kegiatan->sesiAbsensi()->orderBy('waktu_mulai', 'asc')->get();
        } else {
            $this->sesiUntukExport = SesiAbsensi::where('id', $this->id_sesi)->get();
        }
    }

    /**
     * Membuat header kolom secara dinamis.
     */
    public function headings(): array
    {
        // Header kolom statis
        $headings = [
            'No',
            'Nama Peserta',
            // 'NIM',
            'Kelompok',
        ];

        // Tambahkan nama setiap sesi yang diekspor sebagai header kolom
        foreach ($this->sesiUntukExport as $sesi) {
            $headings[] = $sesi->nama;
        }

        return $headings;
    }

    /**
     * Membangun koleksi data untuk diekspor.
     */
    public function collection()
    {
        // 1. Ambil semua peserta yang terdaftar di kegiatan ini
        $peserta = $this->kegiatan->peserta()->orderBy('nama', 'asc')->get();
        
        // 2. Buat "peta" kehadiran yang efisien untuk pencarian cepat
        // Strukturnya: [id_peserta] => [id_sesi_1 => true, id_sesi_2 => true]
        $kehadiranPeserta = Absensi::whereIn('id_sesi', $this->sesiUntukExport->pluck('id'))
            ->get()
            ->groupBy('id_peserta')
            ->map(fn($items) => $items->pluck('id_sesi')->flip());

        // 3. Bangun koleksi baris demi baris untuk file Excel
        $exportCollection = new Collection();
        foreach ($peserta as $index => $p) {
            // Data dasar untuk setiap peserta
            $row = [
                'No' => $index + 1,
                'Nama Peserta' => $p->nama,
                // 'NIM' => $p->nim ?? '-',
                'Kelompok' => $p->kelompok ?? '-',
            ];

            // Cek kehadiran untuk setiap sesi dan tambahkan 'v' atau ''
            foreach ($this->sesiUntukExport as $sesi) {
                // Cek apakah peserta ini (id: $p->id) hadir di sesi ini (id: $sesi->id)
                $hadir = isset($kehadiranPeserta[$p->id]) && isset($kehadiranPeserta[$p->id][$sesi->id]);
                
                // Tambahkan 'v' jika hadir, atau string kosong jika tidak
                $row[$sesi->nama] = $hadir ? 'v' : '';
            }
            $exportCollection->push($row);
        }

        return $exportCollection;
    }
}

