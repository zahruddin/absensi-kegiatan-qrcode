<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\KategoriAbsensi;
use Illuminate\Http\Request;

class KategoriAbsensiController extends Controller
{
    /**
     * Simpan sesi absensi baru.
     */
    public function store(Request $request, $id_kegiatan)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        KategoriAbsensi::create([
            'id_kegiatan' => $id_kegiatan,
            'nama' => $request->nama,
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
