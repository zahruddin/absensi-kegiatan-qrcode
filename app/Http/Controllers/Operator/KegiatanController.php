<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kegiatan;
use App\Models\SesiAbsensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // <-- 1. Import Storage
use Illuminate\Support\Str;              // <-- 2. Import Str
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

        // Langkah 1: Buat data kegiatan terlebih dahulu untuk mendapatkan ID-nya
        $kegiatan = Kegiatan::create([
            'id_user' => Auth::id(),
            'nama' => $request->nama,
            'date' => $request->date,
            'status' => 'aktif',
            'qrcode' => '', // Dikosongkan sementara
        ]);

        // Langkah 2: Buat konten QR, yaitu URL ke halaman pendaftaran publik
        $qrContent = route('kegiatan.register.show', $kegiatan->id); 

        // Langkah 3: Buat nama file yang aman dan unik
        $safeName = Str::slug($kegiatan->nama);
        $fileName = 'kegiatan_qr_' . $safeName . '_' . $kegiatan->id . '.png';
        $filePath = 'qrcode_kegiatan/' . $fileName; // Path di dalam storage/app/public

        // Langkah 4: Generate dan simpan gambar QR Code
        $qrCodeImage = QrCode::format('png')->size(300)->margin(2)->generate($qrContent);
        Storage::disk('public')->put($filePath, $qrCodeImage);

        // Langkah 5: Update record kegiatan dengan path QR Code yang benar
        $kegiatan->update(['qrcode' => $filePath]);

        return redirect()->back()->with('success', 'Kegiatan berhasil ditambahkan beserta QR Code pendaftaran.');
    }
    
    /**
     * Menampilkan halaman detail untuk sebuah kegiatan.
     * Menggunakan Route Model Binding untuk mengambil data Kegiatan secara otomatis.
     */
    public function detail(Kegiatan $kegiatan)
    {
        dd(Auth::check(), Auth::id());
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

    public function downloadQRCode(Kegiatan $kegiatan)
    {
        // 1. Pastikan kegiatan memiliki QR code
        if (!$kegiatan->qrcode) {
            return redirect()->back()->with('error', 'Kegiatan ini tidak memiliki QR Code.');
        }

        // 2. Cek apakah file benar-benar ada di storage
        if (!Storage::disk('public')->exists($kegiatan->qrcode)) {
            return redirect()->back()->with('error', 'File QR Code tidak ditemukan di server.');
        }

        // 3. Buat nama file yang akan diunduh oleh pengguna
        $fileName = 'qrcode-' . Str::slug($kegiatan->nama) . '.png';

        // 4. Ambil path lengkap ke file dan kirim sebagai respons unduhan
        $filePath = Storage::disk('public')->path($kegiatan->qrcode);
        
        return response()->download($filePath, $fileName);
    }
}