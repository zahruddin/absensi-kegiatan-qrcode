<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Sale; // Model untuk transaksi penjualan
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk akses pengguna yang login
use Illuminate\Pagination\Paginator;


class RiwayatController extends Controller
{
    /**
     * Menampilkan daftar riwayat transaksi untuk pengguna yang sedang login.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Ambil seluruh transaksi yang dimiliki oleh pengguna yang sedang login, urutkan berdasarkan tanggal terbaru
        $sales = Sale::where('id_user', Auth::id()) // Filter berdasarkan ID pengguna yang sedang login
                    ->orderByDesc('created_at') // Urutkan berdasarkan tanggal terbaru
                    ->paginate(10); // Menampilkan 10 data per halaman

        // Mengirimkan data transaksi ke view
        return view('customer.riwayat', compact('sales'));
    }
}
