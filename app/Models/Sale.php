<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales';

    protected $fillable = [
        'id_user',
        'id_meja',
        'total_harga',
        'total_diskon',
        'total_bayar',
        'metode_bayar',
        'status_bayar',
        'status_pesanan',
    ];

    // Relasi ke user (kasir)
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Relasi ke detail penjualan
    public function details()
    {
        return $this->hasMany(SaleDetail::class, 'id_sale');
    }
    public function meja()
    {
        return $this->belongsTo(Table::class, 'id_meja'); 
    }

}
