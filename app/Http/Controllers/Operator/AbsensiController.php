<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\SesiAbsensi;
use App\Models\Peserta;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use App\Exports\AbsensiExport; 
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;


class AbsensiController extends Controller
{
    /**
     * Menampilkan daftar peserta yang sudah diabsen pada sesi tertentu.
     */
    public function show(SesiAbsensi $sesi_absensi)
    {
        // 1. Ambil data kegiatan dan semua peserta yang terdaftar di kegiatan tersebut
        $kegiatan = $sesi_absensi->kegiatan()->with('peserta')->first();
        $semuaPesertaKegiatan = $kegiatan->peserta;

        // 2. Ambil data Absensi untuk sesi ini, dan "eager load" data Peserta-nya
        // Ini adalah perubahan utama. Kita sekarang mengambil koleksi Absensi.
        $absensiHadir = Absensi::where('id_sesi', $sesi_absensi->id)
                            ->with('peserta') // Muat relasi peserta untuk efisiensi
                            ->orderBy('waktu_absen', 'asc')
                            ->get();

        // 3. Dapatkan daftar ID dari peserta yang sudah hadir
        $pesertaHadirIds = $absensiHadir->pluck('peserta.id');

        // 4. Saring untuk mendapatkan daftar peserta yang BELUM hadir
        $pesertaBelumHadir = $semuaPesertaKegiatan->whereNotIn('id', $pesertaHadirIds);

        // 5. Siapkan data statistik (logikanya tetap sama, tapi sumber datanya lebih baik)
        $statistik = [
            'totalPeserta' => $semuaPesertaKegiatan->count(),
            'jumlahHadir' => $absensiHadir->count(),
            'jumlahBelumHadir' => $pesertaBelumHadir->count(),
        ];

        // 6. Kirim semua data ke view
        return view('operator.absensiShow', [
            'sesi_absensi'      => $sesi_absensi,
            'kegiatan'          => $kegiatan,
            'absensiHadir'      => $absensiHadir, // <-- Kirim koleksi Absensi, bukan Peserta
            'pesertaBelumHadir' => $pesertaBelumHadir,
            'statistik'         => $statistik,
        ]);
    }

     public function exportAbsensi(Kegiatan $kegiatan, $id_sesi)
    {
        $sesiText = ($id_sesi === 'all') ? 'semua-sesi' : 'sesi-' . $id_sesi;
        $fileName = 'absensi-' . Str::slug($kegiatan->nama) . '-' . $sesiText . '.xlsx';
        
        return Excel::download(new AbsensiExport($kegiatan, $id_sesi), $fileName);
    }

    public function scan(SesiAbsensi $sesi_absensi)
    {
        // Load relasi kegiatan untuk ditampilkan di view
        $sesi_absensi->load('kegiatan');
        
        return view('operator.scan', compact('sesi_absensi'));
    }

    /**
     * Memproses hasil pindaian QR Code yang dikirim via AJAX.
     */
    public function processScan(Request $request)
    {
        // 1. Validasi data yang masuk
        $request->validate([
            'token'   => 'required|string',
            'id_sesi' => 'required|exists:sesi_absensi,id',
        ]);

        // 2. Cari peserta berdasarkan token unik dari QR Code
        $peserta = Peserta::where('token', $request->token)->first();

        // 3. Jika token tidak valid atau peserta tidak ditemukan
        if (!$peserta) {
            return response()->json([
                'status'  => 'error',
                'message' => 'QR Code tidak valid atau tidak terdaftar.'
            ], 404);
        }

        // 4. Periksa apakah peserta sudah diabsen di sesi ini sebelumnya
        $isAlreadyAbsen = Absensi::where('id_peserta', $peserta->id)
                                 ->where('id_sesi', $request->id_sesi)
                                 ->exists();
        
        if ($isAlreadyAbsen) {
            return response()->json([
                'status'  => 'warning',
                'message' => 'Peserta ' . $peserta->nama . ' sudah diabsen sebelumnya di sesi ini.'
            ], 409); // 409 Conflict
        }

        // 5. Jika semua pemeriksaan lolos, buat record absensi baru
        Absensi::create([
            'id_peserta'  => $peserta->id,
            'id_sesi'     => $request->id_sesi,
            'waktu_absen' => now(),
        ]);

        // 6. Kirim respons sukses
        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil! Absensi untuk ' . $peserta->nama . ' telah dicatat.'
        ]);
    }
    
    /**
     * Menyimpan data absensi baru secara manual.
     * Mencegah data duplikat jika peserta sudah diabsen di sesi yang sama.
     */
    public function storeManual(Request $request)
    {
        // 1. Validasi input dari form modal
        $request->validate([
            'id_peserta' => 'required|exists:peserta,id',
            'id_sesi'    => 'required|exists:sesi_absensi,id',
        ]);

        // 2. Gunakan firstOrCreate untuk menyimpan data dengan aman
        // Ini akan mencegah duplikasi jika peserta sudah diabsen pada sesi yang sama.
        Absensi::firstOrCreate(
            [
                'id_peserta' => $request->id_peserta,
                'id_sesi'    => $request->id_sesi,
            ],
            [
                // Kolom ini hanya akan diisi jika data BARU dibuat
                'waktu_absen' => now(), 
            ]
        );

        return redirect()->back()->with('success', 'Peserta berhasil diabsen.');
    }

    /**
     * Membatalkan (menghapus) data absensi yang sudah ada.
     */
    public function cancelManual(Request $request)
    {
        // 1. Validasi input dari form modal
        $request->validate([
            'id_peserta' => 'required|exists:peserta,id',
            'id_sesi'    => 'required|exists:sesi_absensi,id',
        ]);

        // 2. Cari dan hapus record absensi yang cocok
        Absensi::where('id_peserta', $request->id_peserta)
               ->where('id_sesi', $request->id_sesi)
               ->delete();

        return redirect()->back()->with('success', 'Absensi untuk peserta berhasil dibatalkan.');
    }
}