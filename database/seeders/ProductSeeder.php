<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            // Minuman
            ['nama_produk' => 'Americano', 'harga_produk' => 18000, 'stok_produk' => 50, 'status' => 'aktif'],
            ['nama_produk' => 'Cappuccino', 'harga_produk' => 22000, 'stok_produk' => 40, 'status' => 'aktif'],
            ['nama_produk' => 'Latte', 'harga_produk' => 23000, 'stok_produk' => 35, 'status' => 'aktif'],
            ['nama_produk' => 'Espresso', 'harga_produk' => 16000, 'stok_produk' => 60, 'status' => 'aktif'],
            ['nama_produk' => 'Matcha Latte', 'harga_produk' => 25000, 'stok_produk' => 30, 'status' => 'aktif'],
            ['nama_produk' => 'Chocolate Frappe', 'harga_produk' => 27000, 'stok_produk' => 25, 'status' => 'aktif'],
            ['nama_produk' => 'Vanilla Milkshake', 'harga_produk' => 24000, 'stok_produk' => 20, 'status' => 'aktif'],
            ['nama_produk' => 'Lemon Tea', 'harga_produk' => 12000, 'stok_produk' => 70, 'status' => 'aktif'],
            ['nama_produk' => 'Lychee Tea', 'harga_produk' => 13000, 'stok_produk' => 60, 'status' => 'aktif'],
            ['nama_produk' => 'Mineral Water', 'harga_produk' => 6000, 'stok_produk' => 100, 'status' => 'aktif'],

            // Makanan
            ['nama_produk' => 'Chicken Teriyaki Rice Bowl', 'harga_produk' => 30000, 'stok_produk' => 30, 'status' => 'aktif'],
            ['nama_produk' => 'Beef Blackpepper Rice Bowl', 'harga_produk' => 32000, 'stok_produk' => 25, 'status' => 'aktif'],
            ['nama_produk' => 'Spaghetti Bolognese', 'harga_produk' => 28000, 'stok_produk' => 20, 'status' => 'aktif'],
            ['nama_produk' => 'Spaghetti Carbonara', 'harga_produk' => 29000, 'stok_produk' => 20, 'status' => 'aktif'],
            ['nama_produk' => 'French Fries', 'harga_produk' => 15000, 'stok_produk' => 50, 'status' => 'aktif'],
            ['nama_produk' => 'Chicken Wings', 'harga_produk' => 27000, 'stok_produk' => 25, 'status' => 'aktif'],
            ['nama_produk' => 'Mozzarella Sticks', 'harga_produk' => 26000, 'stok_produk' => 15, 'status' => 'aktif'],
            ['nama_produk' => 'Toast Bread & Cheese', 'harga_produk' => 16000, 'stok_produk' => 35, 'status' => 'aktif'],
            ['nama_produk' => 'Waffle with Ice Cream', 'harga_produk' => 25000, 'stok_produk' => 20, 'status' => 'aktif'],
            ['nama_produk' => 'Chocolate Banana Pancake', 'harga_produk' => 24000, 'stok_produk' => 15, 'status' => 'aktif'],
            ['nama_produk' => 'Nasi Goreng Spesial', 'harga_produk' => 27000, 'stok_produk' => 40, 'status' => 'aktif'],
            ['nama_produk' => 'Mie Goreng Jawa', 'harga_produk' => 22000, 'stok_produk' => 45, 'status' => 'aktif'],
            ['nama_produk' => 'Chicken Katsu', 'harga_produk' => 28000, 'stok_produk' => 30, 'status' => 'aktif'],
            ['nama_produk' => 'Sop Iga', 'harga_produk' => 35000, 'stok_produk' => 10, 'status' => 'aktif'],
            ['nama_produk' => 'Dimsum', 'harga_produk' => 18000, 'stok_produk' => 50, 'status' => 'aktif'],
        ];

        foreach ($products as &$product) {
            $product['gambar'] = 'images/products/default.jpg';
            $product['deskripsi'] = 'Menu favorit pelanggan.';
            $product['created_at'] = Carbon::now();
            $product['updated_at'] = Carbon::now();
        }

        DB::table('products')->insert($products);
    }
}
