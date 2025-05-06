<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Product extends Model
{
    use HasFactory, SoftDeletes;
    // use LogActivityAuto; 

    // Nama tabel (opsional, jika berbeda dari nama default)
    protected $table = 'products';

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'nama_produk',
        'gambar',
        'harga_produk',
        'stok_produk',
        'deskripsi',
        'status',
    ];

    // Tipe data yang harus dikonversi
    protected $casts = [
        'harga_produk' => 'decimal:2', // Harga dikonversi ke format decimal
        'stok_produk' => 'integer', // Stok dikonversi ke integer
        'status' => 'string', // Status tetap sebagai string (enum)
    ];
}
