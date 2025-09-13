<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\KategoriAbsensi;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * Absen manual peserta.
     *
     * @param  int  $id  ID peserta
     * @param  int  $kategoriId  ID sesi absensi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function manual(Request $request, $id_peserta)
    {
        $request->validate([
            'id_kategori' => 'required|exists:kategori_absensi,id',
        ]);

        Absensi::create([
            'id_peserta' => $id_peserta,
            'id_kategori' => $request->id_kategori,
            'datetime' => now(),
        ]);

        return back()->with('success', 'Absensi manual berhasil ditambahkan.');
    }
    public function cancel(Request $request, $id_peserta)
    {
        $request->validate([
            'id_kategori' => 'required|exists:kategori_absensi,id',
        ]);

        $absensi = Absensi::where('id_peserta', $id_peserta)
                        ->where('id_kategori', $request->id_kategori)
                        ->first();

        if (!$absensi) {
            return back()->with('error', 'Peserta belum absen di sesi ini.');
        }

        $absensi->delete();

        return back()->with('success', 'Absensi peserta berhasil dibatalkan.');
    }


}
