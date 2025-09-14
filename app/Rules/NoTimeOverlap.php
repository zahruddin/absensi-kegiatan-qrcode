<?php

namespace App\Rules;

use App\Models\SesiAbsensi;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Request;

class NoTimeOverlap implements ValidationRule
{
    private $request;
    private $id_kegiatan;
    private $ignoreId;

    /**
     * Buat instance rule baru.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id_kegiatan
     * @param int|null $ignoreId  // ID sesi yang akan diabaikan (untuk proses update)
     */
    public function __construct(Request $request, int $id_kegiatan, int $ignoreId = null)
    {
        $this->request = $request;
        $this->id_kegiatan = $id_kegiatan;
        $this->ignoreId = $ignoreId;
    }

    /**
     * Jalankan aturan validasi.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Gabungkan tanggal dan jam dari input form
        $waktu_mulai = $this->request->input('tanggal_mulai') . ' ' . $this->request->input('jam_mulai');
        $waktu_selesai = $this->request->input('tanggal_selesai') . ' ' . $this->request->input('jam_selesai');

        // Query untuk mencari sesi yang tumpang tindih
        $query = SesiAbsensi::where('id_kegiatan', $this->id_kegiatan)
            ->where(function ($query) use ($waktu_mulai, $waktu_selesai) {
                // Kondisi 1: Waktu mulai/selesai yang baru berada di dalam rentang sesi yang sudah ada
                $query->where(function($q) use ($waktu_mulai, $waktu_selesai) {
                    $q->whereBetween('waktu_mulai', [$waktu_mulai, $waktu_selesai])
                      ->orWhereBetween('waktu_selesai', [$waktu_mulai, $waktu_selesai]);
                // Kondisi 2: Sesi yang baru "menelan" seluruh rentang waktu sesi yang sudah ada
                })->orWhere(function($q) use ($waktu_mulai, $waktu_selesai) {
                    $q->where('waktu_mulai', '<=', $waktu_mulai)
                      ->where('waktu_selesai', '>=', $waktu_selesai);
                });
            });

        // Jika ini adalah proses update, abaikan data sesi itu sendiri dari pengecekan
        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        $overlappingSession = $query->first();

        // Jika ditemukan sesi yang tumpang tindih, gagalkan validasi
        if ($overlappingSession) {
            $fail('Waktu sesi ini bertabrakan dengan sesi "' . $overlappingSession->nama . '".');
        }
    }
}