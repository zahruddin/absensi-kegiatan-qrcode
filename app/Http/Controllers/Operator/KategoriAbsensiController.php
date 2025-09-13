<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\SesiAbsensi;
use Illuminate\Http\Request;

class KategoriAbsensiController extends Controller
{
    /**
     * Simpan sesi absensi baru.
     */
    public function store(Request $request, $id_kegiatan)
    {
        // 1. Validasi untuk input yang terpisah
        $request->validate([
            'nama'            => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date_format:Y-m-d',
            'jam_mulai'       => 'required|date_format:H:i',
            'tanggal_selesai' => 'required|date_format:Y-m-d',
            'jam_selesai'     => 'required|date_format:H:i',
        ]);

        // 2. Gabungkan tanggal dan jam menjadi format datetime
        $waktu_mulai = $request->tanggal_mulai . ' ' . $request->jam_mulai;
        $waktu_selesai = $request->tanggal_selesai . ' ' . $request->jam_selesai;

        // 3. Validasi tambahan setelah digabung
        $validator = \Validator::make(['waktu_selesai' => $waktu_selesai], [
            'waktu_selesai' => 'after:' . $waktu_mulai
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors(['tanggal_selesai' => 'Waktu selesai harus setelah waktu mulai.'])
                ->withInput();
        }
        
        // 4. Simpan ke database
        SesiAbsensi::create([
            'id_kegiatan'   => $id_kegiatan,
            'nama'          => $request->nama,
            'waktu_mulai'   => $waktu_mulai,
            'waktu_selesai' => $waktu_selesai,
        ]);

        return redirect()->route('operator.kegiatan.detail', $id_kegiatan)
            ->with('success', 'Sesi absensi berhasil ditambahkan.');
    }

    /**
     * Update sesi absensi.
     */
    public function update(Request $request, $id_kegiatan, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $kategori = KategoriAbsensi::findOrFail($id);
        $kategori->update([
            'nama' => $request->nama,
        ]);

        return redirect()->route('operator.kegiatan.detail', $id_kegiatan)
            ->with('success', 'Sesi absensi berhasil diperbarui.');
    }

    /**
     * Hapus sesi absensi.
     */
    public function delete($id_kegiatan, $id)
    {
        $kategori = KategoriAbsensi::findOrFail($id);
        $kategori->delete();

        return redirect()->route('operator.kegiatan.detail', $id_kegiatan)
            ->with('success', 'Sesi absensi berhasil dihapus.');
    }
}
