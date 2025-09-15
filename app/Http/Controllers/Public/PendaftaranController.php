<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PendaftaranController extends Controller
{
    public function show(Kegiatan $kegiatan)
    {
        // Cek apakah pengguna sudah mendaftar di kegiatan ini
        $isRegistered = false;
        if (Auth::check()) {
            $isRegistered = Peserta::where('id_kegiatan', $kegiatan->id)
                                   ->where('id_user', Auth::id())
                                   ->exists();
        }

        return view('public.pendaftaran', compact('kegiatan', 'isRegistered'));
    }

    public function store(Request $request, Kegiatan $kegiatan)
    {
        // Cek lagi untuk mencegah pendaftaran ganda
        $isRegistered = Peserta::where('id_kegiatan', $kegiatan->id)
                               ->where('id_user', Auth::id())
                               ->exists();

        if ($isRegistered) {
            return redirect()->route('kegiatan.register.show', $kegiatan->id)
                             ->with('warning', 'Anda sudah terdaftar di kegiatan ini.');
        }
        
        // Buat data peserta baru yang terhubung dengan user dan kegiatan
        Peserta::create([
            'id_kegiatan' => $kegiatan->id,
            'id_user'     => Auth::id(),
            'nama'        => Auth::user()->name,
            'email'       => Auth::user()->email,
            // Anda bisa menambahkan kolom lain di sini jika perlu
        ]);
        
        // Redirect kembali ke halaman pendaftaran dengan pesan sukses
        return redirect()->route('kegiatan.register.show', $kegiatan->id)
                         ->with('success', 'Selamat! Anda berhasil terdaftar sebagai peserta.');
    }
}