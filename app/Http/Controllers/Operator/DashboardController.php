<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Kegiatan;
use App\Models\Peserta;
use App\Models\Absensi;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id(); // ✅ ambil ID user yang sedang login (operator)

        // Ambil semua kegiatan milik operator login
        $kegiatanIds = Kegiatan::where('id_user', $userId)->pluck('id');

        // Hitung data berdasarkan operator login
        $totalKegiatan = $kegiatanIds->count();
        $totalPeserta  = Peserta::whereIn('id_kegiatan', $kegiatanIds)->count();
        $totalAbsensi  = Absensi::whereHas('kategori.kegiatan', function ($query) use ($userId) {
            $query->where('id_user', $userId);
        })->count();

        // Peserta belum hadir
        $belumHadir = $totalPeserta - $totalAbsensi;

        // Ambil 5 kegiatan terbaru milik operator login
        $kegiatanTerbaru = Kegiatan::where('id_user', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('operator.dashboard', compact(
            'totalKegiatan',
            // 'totalPeserta',
            // 'totalAbsensi',
            // 'belumHadir',
            'kegiatanTerbaru'
        ));
    }
}
