<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Absensi;
use App\Models\KategoriAbsensi;
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
        // Ambil kegiatan berdasarkan ID
        $kegiatan = Kegiatan::findOrFail($id);

        // Ambil semua kategori absensi (sesi) milik kegiatan
        $kategoriAbsensi = KategoriAbsensi::where('id_kegiatan', $id)->get();

        // Ambil semua absensi yang terkait dengan kategori-kategori di atas
        $absensi = Absensi::whereIn('id_kategori', $kategoriAbsensi->pluck('id'))
            ->with(['kategori', 'peserta']) // relasi kategori & peserta
            ->get();

        return view('operator.kegiatanDetail', compact('kegiatan', 'absensi', 'kategoriAbsensi'));
    }
        


}
