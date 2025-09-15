<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kegiatan;
use App\Models\SesiAbsensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KegiatanController extends Controller
{
    /**
     * Menampilkan halaman utama Kelola Kegiatan.
     * Query dioptimalkan dengan withCount untuk mengambil jumlah peserta dan sesi.
     */
    public function index()
    {
        $kegiatans = Kegiatan::where('id_user', auth()->id())
            ->withCount(['peserta', 'sesiAbsensi']) // Menghitung relasi secara efisien
            ->orderBy('date', 'desc')
            ->paginate(10); // Menggunakan pagination

        return view('operator.kegiatan', compact('kegiatans'));
    }

    /**
     * Menyimpan kegiatan baru ke database.
     * Disederhanakan agar sesuai dengan form di modal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        // Tidak perlu generate QR untuk kegiatan itu sendiri, karena tidak digunakan
        Kegiatan::create([
            'id_user' => Auth::id(),
            'nama' => $request->nama,
            'date' => $request->date,
            'status' => 'aktif', // Status default saat dibuat
        ]);

        return redirect()->back()->with('success', 'Kegiatan baru berhasil ditambahkan.');
    }
    
    /**
     * Menampilkan halaman detail untuk sebuah kegiatan.
     * Menggunakan Route Model Binding untuk mengambil data Kegiatan secara otomatis.
     */
    public function detail(Kegiatan $kegiatan)
    {
        // Memastikan operator hanya bisa melihat kegiatannya sendiri (keamanan tambahan)
        if ($kegiatan->id_user !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke kegiatan ini.');
        }

        // Muat relasi sesiAbsensi beserta hitungan absensinya
        $kegiatan->load(['sesiAbsensi' => function ($query) {
            $query->withCount('absensi')->orderBy('waktu_mulai', 'asc');
        }]);
        $sesiAbsensi = $kegiatan->sesiAbsensi;

        // --- Logika Statistik (sudah benar) ---
        $totalPeserta = $kegiatan->peserta()->count();
        $sesiIds = $sesiAbsensi->pluck('id');
        $jumlahHadirUnik = Absensi::whereIn('id_sesi', $sesiIds)->distinct('id_peserta')->count();
        $tingkatPartisipasi = ($totalPeserta > 0) ? round(($jumlahHadirUnik / $totalPeserta) * 100) : 0;
        $maksimalAbsensi = $totalPeserta * $sesiAbsensi->count();
        $totalAbsensiTercatat = Absensi::whereIn('id_sesi', $sesiIds)->count();
        $tingkatKehadiranTotal = ($maksimalAbsensi > 0) ? round(($totalAbsensiTercatat / $maksimalAbsensi) * 100) : 0;
        $sesiAktif = $sesiAbsensi->first(fn($sesi) => now()->between($sesi->waktu_mulai, $sesi->waktu_selesai));
        $sesiSelesai = $sesiAbsensi->where('waktu_selesai', '<', now())->count();
        
        $kehadiranPeserta = Absensi::whereIn('id_sesi', $sesiIds)
                               ->get()
                               ->groupBy('id_peserta')
                               ->map(fn($items) => $items->pluck('id_sesi'));

        return view('operator.kegiatanDetail', [
            'kegiatan' => $kegiatan,
            'sesiAbsensi' => $sesiAbsensi,
            'kehadiranPeserta' => $kehadiranPeserta,
            'sesiAktif' => $sesiAktif,
            'sesiSelesai' => $sesiSelesai,
            'tingkatPartisipasi' => $tingkatPartisipasi,
            'tingkatKehadiranTotal' => $tingkatKehadiranTotal,
        ]);
    }

    /**
     * Memperbarui data kegiatan.
     * Menggunakan Route Model Binding untuk mengambil data Kegiatan secara otomatis.
     */
    public function update(Request $request, Kegiatan $kegiatan)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $kegiatan->update($request->only(['nama', 'date']));

        return redirect()->back()->with('success', 'Kegiatan berhasil diperbarui.');
    }

    /**
     * Menghapus kegiatan.
     * Menggunakan Route Model Binding dan nama method 'destroy' sesuai konvensi.
     */
    public function destroy(Kegiatan $kegiatan)
    {
        // Model event akan menangani penghapusan file QR
        // Aturan onDelete('cascade') di database akan menangani data terkait (sesi, peserta, absensi)
        $kegiatan->delete();

        return redirect()->route('operator.kegiatan.index')
                         ->with('success', 'Kegiatan dan semua datanya berhasil dihapus.');
    }
}