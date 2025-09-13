<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriAbsensi extends Model
{
    use HasFactory;

    protected $table = 'kategori_absensi';

    protected $fillable = [
        'id_kegiatan',
        'nama',
    ];

    /**
     * Relasi ke model Kegiatan.
     * Satu Kategori Absensi dimiliki oleh satu Kegiatan.
     */
    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan');
    }

    /**
     * Relasi ke model Absensi.
     * Satu Kategori Absensi bisa memiliki banyak data Absensi.
     */
    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'id_kategori');
    }
}