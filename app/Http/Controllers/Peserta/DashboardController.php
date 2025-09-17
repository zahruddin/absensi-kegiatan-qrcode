<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard peserta.
     */
    public function index()
    {
        // Ambil semua kegiatan yang masih aktif atau akan datang
        $kegiatans = Kegiatan::where('status', 'aktif')
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date', 'asc')
            ->get();

        // Ambil data kepesertaan user yang sedang login untuk mengecek status pendaftaran
        $pesertaData = Peserta::where('id_user', Auth::id())->get()->keyBy('id_kegiatan');

        return view('peserta.dashboard', compact('kegiatans', 'pesertaData'));
    }

    /**
     * Menangani proses pendaftaran peserta ke sebuah kegiatan.
     */
    public function register(Request $request, Kegiatan $kegiatan)
    {
        $user = Auth::user();

        // ✅ DIUBAH: Validasi hanya untuk field yang bisa diisi oleh user
        $request->validate([
            'nim'      => 'nullable|string|max:250',
            'no_hp'    => 'nullable|string|max:250',
            'prodi'    => 'nullable|string|max:250',
            'kelompok' => 'nullable|string|max:250',
        ]);

        $peserta = Peserta::firstOrCreate(
            [
                'id_kegiatan' => $kegiatan->id,
                'id_user'     => $user->id,
            ],
            [
                // ✅ DIUBAH: Ambil nama & email langsung dari Auth, bukan dari request
                'nama'        => $user->name,
                'email'       => $user->email,
                'token'       => Str::random(40),
                // Isi dengan data baru dari form
                'nim'         => $request->nim,
                'no_hp'       => $request->no_hp,
                'prodi'       => $request->prodi,
                'kelompok'    => $request->kelompok,
            ]
        );

        // Cek apakah ini pendaftaran baru
        if ($peserta->wasRecentlyCreated) {
            
            // Generate QR Code hanya jika pendaftaran baru berhasil
            $safeName = Str::slug($user->name);
            $uniquePart = substr($peserta->token, 0, 8);
            $fileName = 'qrcode_' . $safeName . '_' . $uniquePart . '.png';
            $filePath = 'qrcode_peserta/' . $fileName;

            $qrCodeImage = QrCode::format('png')->size(300)->margin(2)->generate($peserta->token);
            Storage::disk('public')->put($filePath, $qrCodeImage);

            // Update record peserta dengan path qrcode
            $peserta->update(['qrcode' => $filePath]);

            return redirect()->back()->with('success', 'Anda berhasil mendaftar! QR Code Anda telah dibuat.');
        } else {
            return redirect()->back()->with('error', 'Anda sudah terdaftar di kegiatan ini.');
        }
    }





}