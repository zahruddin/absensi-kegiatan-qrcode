<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'id_peserta',
        'id_sesi',
        'waktu_absen',
    ];

    /**
     * Relasi ke model Peserta.
     * Satu data Absensi dimiliki oleh satu Peserta.
     */
    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class, 'id_peserta');
    }

    /**
     * Relasi ke model KategoriAbsensi.
     * Satu data Absensi merujuk pada satu Kategori Absensi.
     */
    public function sesiAbsensi(): BelongsTo
    {
        return $this->belongsTo(KategoriAbsensi::class, 'id_sesi');
    }
}