<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesiAbsensi extends Model
{
    use HasFactory;

    /**
     * Menentukan nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'sesi_absensi';

    /**
     * Kolom yang diizinkan untuk diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_kegiatan',
        'nama',
        'status',
        'waktu_mulai',
        'waktu_selesai',
    ];

    /**
     * Tipe data native yang akan di-casting secara otomatis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'waktu_mulai'   => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relasi Eloquent
    |--------------------------------------------------------------------------
    */

    /**
     * Mendefinisikan relasi "belongsTo" ke model Kegiatan.
     * Satu Sesi Absensi dimiliki oleh satu Kegiatan.
     */
    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan');
    }

    /**
     * Mendefinisikan relasi "hasMany" ke model Absensi.
     * Satu Sesi Absensi memiliki banyak data Absensi.
     */
    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'id_sesi');
    }
}