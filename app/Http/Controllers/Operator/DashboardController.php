<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Peserta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Ambil semua kegiatan milik operator ini sekali saja untuk efisiensi
        $semuaKegiatan = Kegiatan::where('id_user', $userId)->get();
        $kegiatanIds = $semuaKegiatan->pluck('id');

        // 1. Siapkan data untuk Kartu Statistik
        $totalKegiatan = $semuaKegiatan->count();
        $kegiatanAkanDatang = $semuaKegiatan->where('date', '>', now()->format('Y-m-d'))->count();
        $kegiatanHariIni = $semuaKegiatan->where('date', '=', now()->format('Y-m-d'))->count();
        $totalPeserta = Peserta::whereIn('id_kegiatan', $kegiatanIds)->count();

        // 2. Siapkan data untuk Tabel
        $kegiatanMendatang = $semuaKegiatan
            ->where('date', '>=', now()->format('Y-m-d'))
            ->sortBy('date') // Urutkan dari yang paling dekat
            ->take(5);

        $kegiatanSelesai = $semuaKegiatan
            ->where('date', '<', now()->format('Y-m-d'))
            ->sortByDesc('date') // Urutkan dari yang paling baru selesai
            ->take(5);

        // 3. Siapkan data untuk Grafik (6 bulan terakhir)
        $chartData = Kegiatan::select(
                DB::raw('YEAR(date) as year, MONTH(date) as month'),
                DB::raw('count(*) as count')
            )
            ->where('id_user', $userId)
            ->where('date', '>=', Carbon::now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
        
        $labels = [];
        $data = [];
        // Inisialisasi 6 bulan terakhir dengan data 0
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->isoFormat('MMM Y');
            $data[$month->format('Y-n')] = 0;
        }
        // Isi data dari database
        foreach ($chartData as $item) {
            $data[$item->year . '-' . $item->month] = $item->count;
        }

        // Kirim semua data ke view
        return view('operator.dashboard', [
            'totalKegiatan' => $totalKegiatan,
            'kegiatanAkanDatang' => $kegiatanAkanDatang,
            'kegiatanHariIni' => $kegiatanHariIni,
            'totalPeserta' => $totalPeserta,
            'kegiatanMendatang' => $kegiatanMendatang,
            'kegiatanSelesai' => $kegiatanSelesai,
            'chartLabels' => array_values($labels),
            'chartData' => array_values($data),
        ]);
    }
}