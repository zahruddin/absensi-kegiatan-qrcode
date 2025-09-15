<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Kegiatan extends Model
{
    use HasFactory;

    // Nama tabel jika tidak mengikuti konvensi Laravel (opsional, tapi baik untuk kejelasan)
    protected $table = 'kegiatan';

    protected $casts = [
        'id_user' => 'integer',
        'date' => 'date',
    ];

    protected $fillable = [
        'id_user',
        'nama',
        'qrcode',
        'date',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Kegiatan $kegiatan) {
            // Hapus file QR kegiatan jika ada
            if ($kegiatan->qrcode) {
                Storage::disk('public')->delete($kegiatan->qrcode);
            }
        });
    }

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