<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Kegiatan;
use App\Models\Peserta;
use App\Models\SesiAbsensi;

class ScanController extends Controller
{
    //
    /**
     * Menampilkan halaman scan mandiri untuk kegiatan tertentu.
     */
    public function show(Kegiatan $kegiatan)
    {
        // Cari sesi yang sedang aktif saat ini untuk kegiatan ini
        $sesiAktif = SesiAbsensi::where('id_kegiatan', $kegiatan->id)
            ->where('waktu_mulai', '<=', now())
            ->where('waktu_selesai', '>=', now())
            ->first();

        return view('public.scan-mandiri', compact('kegiatan', 'sesiAktif'));
    }

    /**
     * Memproses hasil pindaian QR Code yang dikirim dari halaman publik.
     */
    public function process(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'id_kegiatan' => 'required|exists:kegiatan,id',
        ]);

        // 1. Cari sesi aktif lagi di backend (lebih aman)
        $sesiAktif = SesiAbsensi::where('id_kegiatan', $request->id_kegiatan)
            ->where('waktu_mulai', '<=', now())
            ->where('waktu_selesai', '>=', now())
            ->first();

        if (!$sesiAktif) {
            return response()->json(['message' => 'Absensi Gagal. Saat ini tidak ada sesi yang sedang berlangsung.'], 409);
        }

        // 2. Cari peserta berdasarkan token dan pastikan ia terdaftar di kegiatan yang benar
        $peserta = Peserta::where('token', $request->token)
                          ->where('id_kegiatan', $request->id_kegiatan)
                          ->first();

        if (!$peserta) {
            return response()->json(['message' => 'QR Code tidak valid untuk kegiatan ini.'], 404);
        }

        // 3. Gunakan firstOrCreate untuk mencatat absensi dan mencegah duplikasi
        $absensi = Absensi::firstOrCreate(
            [
                'id_peserta' => $peserta->id,
                'id_sesi'    => $sesiAktif->id,
            ],
            [
                'waktu_absen' => now(), 
            ]
        );
        
        // Cek apakah record baru saja dibuat atau sudah ada sebelumnya
        if ($absensi->wasRecentlyCreated) {
            return response()->json(['message' => 'Berhasil! Absensi untuk ' . $peserta->nama . ' telah dicatat di sesi '.$sesiAktif->nama]);
        } else {
            return response()->json(['message' => 'Anda sudah diabsen sebelumnya di sesi ini, ' . $peserta->nama . '.'], 409);
        }
    }
}
