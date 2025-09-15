<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage; 

class Peserta extends Model
{
    use HasFactory;

    protected $table = 'peserta';

    protected $fillable = [
        'id_kegiatan',
        'nama',
        'email',
        'no_hp',
        'prodi',
        'nim',
        'kelompok',
        'qrcode',
        'token',
    ];

     /**
     * ✅ TAMBAHKAN BLOK KODE INI
     * Menjalankan logika tambahan saat model event terjadi.
     */
    protected static function booted(): void
    {
        // Fungsi ini akan berjalan OTOMATIS setiap kali model Peserta akan dihapus
        static::deleting(function (Peserta $peserta) {
            // Cek jika peserta memiliki path qrcode yang tersimpan
            if ($peserta->qrcode) {
                // Hapus file dari storage menggunakan path yang tersimpan
                Storage::disk('public')->delete($peserta->qrcode);
            }
        });
    }

    /**
     * Relasi ke model Kegiatan.
     * Satu Peserta terdaftar di satu Kegiatan.
     */
    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan');
    }

    /**
     * Relasi ke model Absensi.
     * Satu Peserta bisa memiliki banyak catatan Absensi.
     */
    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'id_peserta');
    }

    public function user(): BelongsTo
{
    return $this->belongsTo(User::class, 'id_user');
}
}