<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\SesiAbsensi;
use App\Rules\NoTimeOverlap;
use Illuminate\Http\Request;

class SesiAbsensiController extends Controller
{
    /**
     * Simpan sesi absensi baru.
     * Menggunakan Route Model Binding untuk $kegiatan.
     */
    public function store(Request $request, Kegiatan $kegiatan)
    {
        // Validasi sekarang jauh lebih bersih menggunakan Custom Rule
        $request->validate([
            'nama'            => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date_format:Y-m-d',
            'jam_mulai'       => 'required|date_format:H:i',
            'tanggal_selesai' => ['required', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulai'],
            'jam_selesai'     => [
                'required', 
                'date_format:H:i', 
                new NoTimeOverlap($request, $kegiatan->id)
            ],
        ]);

        // Buat data baru yang terhubung langsung dengan objek $kegiatan
        $kegiatan->sesiAbsensi()->create([
            'nama'          => $request->nama,
            'waktu_mulai'   => $request->tanggal_mulai . ' ' . $request->jam_mulai,
            'waktu_selesai' => $request->tanggal_selesai . ' ' . $request->jam_selesai,
        ]);

        return redirect()->route('operator.kegiatan.detail', $kegiatan->id)
                         ->with('success', 'Sesi absensi berhasil ditambahkan.');
    }

    /**
     * Update sesi absensi yang sudah ada.
     * Menerima objek Kegiatan dan SesiAbsensi secara otomatis.
     */
    public function update(Request $request, Kegiatan $kegiatan, SesiAbsensi $sesi_absensi)
    {
        // Gunakan Validator secara manual agar kita bisa mengontrol redirect
        $validator = \Validator::make($request->all(), [
            'nama'            => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date_format:Y-m-d',
            'jam_mulai'       => 'required|date_format:H:i',
            'tanggal_selesai' => ['required', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulai'],
            'jam_selesai'     => [
                'required', 
                'date_format:H:i', 
                new NoTimeOverlap($request, $kegiatan->id, $sesi_absensi->id)
            ],
        ]);

        // ✅ Jika validasi GAGAL
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator)
                ->with('failed_edit_sesi_id', $sesi_absensi->id); // <-- Kirim ID sesi yang gagal
        }
        
        // Jika validasi SUKSES (logika ini tidak berubah)
        $sesi_absensi->update([
            'nama'          => $request->nama,
            'waktu_mulai'   => $request->tanggal_mulai . ' ' . $request->jam_mulai,
            'waktu_selesai' => $request->tanggal_selesai . ' ' . $request->jam_selesai,
        ]);
        
        return redirect()->route('operator.kegiatan.detail', $kegiatan->id)
                        ->with('success', 'Sesi absensi berhasil diperbarui.');
    }

    /**
     * Hapus sesi absensi.
     * Menggunakan Route Model Binding dan nama method 'destroy'.
     */
    public function destroy(Kegiatan $kegiatan, SesiAbsensi $sesi_absensi)
    {
        $sesi_absensi->delete();

        return redirect()->route('operator.kegiatan.detail', $kegiatan->id)
                         ->with('success', 'Sesi absensi berhasil dihapus.');
    }
}