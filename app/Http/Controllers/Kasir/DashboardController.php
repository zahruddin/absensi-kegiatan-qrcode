<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Sale; // Model untuk transaksi penjualan
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk akses pengguna yang login
use Illuminate\Pagination\Paginator;

class DashboardController extends Controller
{
    //
    public function index(){
         // Ambil seluruh transaksi yang dimiliki oleh pengguna yang sedang login, urutkan berdasarkan tanggal terbaru
         $sales = Sale::orderByDesc('created_at') // Urutkan berdasarkan tanggal terbaru
         ->paginate(10); // Menampilkan 10 data per halaman

        // Mengirimkan data transaksi ke view
        return view('kasir.dashboard', compact('sales'));
    }

    public function proses(Request $request, Sale $sale)
    {
        try {
            // Ubah status pesanan menjadi 'selesai'
            $sale->update([
                'status_pesanan' => 'selesai'
            ]);

            // Kembalikan response JSON agar bisa ditangani oleh JavaScript
            return redirect()->route('kasir.struk', $sale->id);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal memproses pesanan. ' . $e->getMessage()
            ], 500);
        }
    }
    public function struk(Sale $sale)
    {
        $sale->load('details', 'meja', 'user'); // pastikan relasinya ada

        return view('kasir.struk', compact('sale'));
    }

    public function getSalesData()
{
    $sales = Sale::with(['meja', 'details'])->latest()->limit(10)->get();

    return response()->json([
        'html' => view('kasir.partials.sales-table-body', compact('sales'))->render()
    ]);
}

}
