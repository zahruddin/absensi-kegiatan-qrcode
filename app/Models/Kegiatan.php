<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kegiatan extends Model
{
    use HasFactory;

    // Nama tabel jika tidak mengikuti konvensi Laravel (opsional, tapi baik untuk kejelasan)
    protected $table = 'kegiatan';

    protected $fillable = [
        'id_user',
        'nama',
        'qrcode',
        'date',
    ];

    /**
     * Relasi ke model User.
     * Satu Kegiatan dimiliki oleh satu User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Relasi ke model Peserta.
     * Satu Kegiatan memiliki banyak Peserta.
     */
    public function peserta(): HasMany
    {
        return $this->hasMany(Peserta::class, 'id_kegiatan');
    }

    public function sesiAbsensi(): HasMany
    {
        return $this->hasMany(SesiAbsensi::class, 'id_kegiatan');
    }
}