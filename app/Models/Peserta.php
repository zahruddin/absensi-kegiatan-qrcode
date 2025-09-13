<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}