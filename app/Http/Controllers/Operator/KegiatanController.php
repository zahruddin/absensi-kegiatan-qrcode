<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Absensi;
use App\Models\SesiAbsensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; // Gunakan facade File untuk operasi file
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KegiatanController extends Controller
{
    //
    public function index() {
        $kegiatans = Kegiatan::where('id_user', auth()->id())->paginate(10);
        return view('operator.kelolakegiatan' , compact('kegiatans'));
    }
    public function store(Request $request)
    {
        // 1. Validasi input, tambahkan 'date' jika diperlukan dari form
        $request->validate([
            'nama' => 'required|string|max:255',
            // 'date' => 'required|date', // Asumsi tanggal diinput dari form
        ]);

        // 2. Buat data kegiatan terlebih dahulu untuk mendapatkan ID
        $kegiatan = Kegiatan::create([
            'id_user' => Auth::id(),
            'nama'    => $request->nama,
            'date'    => now(), // Gunakan tanggal sekarang atau dari input form
            'qrcode'  => '', // Dikosongkan sementara
        ]);

        // 3. Siapkan konten dan nama file QR Code
        // Gunakan helper route() agar lebih dinamis dan tidak mudah error jika URL berubah
        $qrContent = route('kegiatan.info', $kegiatan->id); 
        $filename  = 'kegiatan-' . $kegiatan->id . '-' . time() . '.png';
        $path      = 'qrcode/' . $filename;

        // 4. Generate QR Code
        $qrCode = QrCode::format('png')
                        ->size(300)
                        ->margin(2)
                        ->generate($qrContent);

        // 5. Simpan QR Code menggunakan Storage facade (cara terbaik di Laravel)
        // Ini akan otomatis membuat folder jika belum ada
        Storage::disk('public')->put($path, $qrCode);

        // 6. Update kolom qrcode di database dengan path yang benar
        $kegiatan->update(['qrcode' => $path]);

        return redirect()->back()->with('success', 'Kegiatan dan QR Code berhasil dibuat!');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'  => 'required|string|max:255',
            // 'status'       => 'required|in:ready,ordered',
        ]);

        $kegiatan = Kegiatan::findOrFail($id);

        // Update data produk tanpa mengganti gambar jika tidak ada gambar baru
        $kegiatan->update([
            'nama'  => $request->nama,
            // 'status'       => $request->status
        ]);

        return redirect()->back()->with('success', 'Kegiatan berhasil diperbarui.');
    }

   

    public function delete($id)
    {
        try {
            // 1. Cari kegiatan berdasarkan ID DAN pastikan pemiliknya adalah user yang login.
            $kegiatan = Kegiatan::where('id', $id)
                                ->where('id_user', auth()->id()) // <-- KUNCI PENGAMANAN
                                ->firstOrFail();
            
            // 2. Ambil path ke file QR code
            // Pastikan kolom qrcode tidak kosong sebelum mencoba menghapus file
            if ($kegiatan->qrcode) {
                // Menggunakan helper public_path() lebih disarankan untuk file di storage/app/public
                $qrCodePath = public_path('storage/qrcode/' . $kegiatan->qrcode);
                
                // 3. Jika file QR code ada, hapus file tersebut
                if (File::exists($qrCodePath)) {
                    File::delete($qrCodePath);
                }
            }

            // 4. Hapus data kegiatan dari database
            $kegiatan->delete();

            // 5. Berikan notifikasi sukses
            session()->flash('success', 'Kegiatan berhasil dihapus!');
            return response()->json(['success' => 'Kegiatan berhasil dihapus!']);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Menangani kasus jika data tidak ditemukan atau bukan milik user
            return response()->json(['error' => 'Data tidak ditemukan atau Anda tidak memiliki akses.'], 404);
            
        } catch (\Exception $e) {
            // Menangani kesalahan umum lainnya
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // KegiatanController.php
public function detail($id)
{
    // 1. Ambil data utama dengan relasi dan hitungan yang dibutuhkan
    $kegiatan = Kegiatan::withCount('peserta')->findOrFail($id);
    $sesiAbsensi = SesiAbsensi::where('id_kegiatan', $id)
                              ->withCount('absensi')
                              ->orderBy('waktu_mulai', 'asc')
                              ->get();

    // 2. Siapkan variabel dasar untuk statistik
    $totalPeserta = $kegiatan->peserta_count;
    $totalSesi = $sesiAbsensi->count();
    $sesiIds = $sesiAbsensi->pluck('id');

    // 3. Hitung Statistik Partisipasi (Berapa persen peserta yang datang min. 1x)
    $jumlahHadirUnik = Absensi::whereIn('id_sesi', $sesiIds)->distinct('id_peserta')->count();
    $tingkatPartisipasi = ($totalPeserta > 0) ? round(($jumlahHadirUnik / $totalPeserta) * 100) : 0;

    // 4. Hitung Statistik Kehadiran Total (Berapa persen total absensi yang terisi)
    $maksimalAbsensi = $totalPeserta * $totalSesi;
    $totalAbsensiTercatat = Absensi::whereIn('id_sesi', $sesiIds)->count();
    $tingkatKehadiranTotal = ($maksimalAbsensi > 0) ? round(($totalAbsensiTercatat / $maksimalAbsensi) * 100) : 0;
    
    // 5. Cari sesi yang sedang aktif dan hitung sesi yang sudah selesai
    $sesiAktif = $sesiAbsensi->first(fn($sesi) => now()->between($sesi->waktu_mulai, $sesi->waktu_selesai));
    $sesiSelesai = $sesiAbsensi->where('waktu_selesai', '<', now())->count();
    
    // 6. Siapkan data untuk tabel kehadiran (tidak berubah)
    $kehadiranPeserta = Absensi::whereIn('id_sesi', $sesiIds)
                               ->get()
                               ->groupBy('id_peserta')
                               ->map(fn($items) => $items->pluck('id_sesi'));

    // 7. Kirim semua data yang sudah dihitung ke view
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

        


}
