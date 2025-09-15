<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Peserta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin dengan data statistik.
     */
    public function index()
    {
        // 1. Ambil data untuk kartu statistik
        $totalKegiatan = Kegiatan::count();
        $totalOperator = User::where('role', 'operator')->count();
        $totalPeserta = Peserta::count();
        $kegiatanAktif = Kegiatan::where('date', '>=', now()->format('Y-m-d'))->count();

        // 2. Ambil 5 kegiatan terbaru dengan relasi yang dibutuhkan (operator & jumlah peserta)
        $kegiatanTerbaru = Kegiatan::with('user')
            ->withCount('peserta')
            ->latest('date')
            ->take(5)
            ->get();

        // 3. Siapkan data untuk grafik (jumlah kegiatan per bulan dalam 6 bulan terakhir)
        $chartData = Kegiatan::select(
                DB::raw('YEAR(date) as year, MONTH(date) as month'),
                DB::raw('count(*) as count')
            )
            ->where('date', '>=', Carbon::now()->subMonths(5)->startOfMonth()) // Mulai dari awal 6 bulan lalu
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
        
        $labels = [];
        $data = [];
        // Inisialisasi 6 bulan terakhir dengan data 0
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->isoFormat('MMM Y'); // Format: Sep 2025
            $data[$month->format('Y-n')] = 0;
        }
        // Isi dengan data dari database
        foreach ($chartData as $item) {
            $data[$item->year . '-' . $item->month] = $item->count;
        }

        // 4. Kirim semua data ke view
        return view('admin.dashboard', [
            'totalKegiatan' => $totalKegiatan,
            'totalOperator' => $totalOperator,
            'totalPeserta' => $totalPeserta,
            'kegiatanAktif' => $kegiatanAktif,
            'kegiatanTerbaru' => $kegiatanTerbaru,
            'chartLabels' => array_values($labels),
            'chartData' => array_values($data),
        ]);
    }
}
