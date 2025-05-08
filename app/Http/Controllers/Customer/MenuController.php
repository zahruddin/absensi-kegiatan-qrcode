<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Table;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class MenuController extends Controller
{
    //
    //
    public function scanQRCode($idmeja)
    {
        // Simpan data meja ke session
        session(['meja_id' => $idmeja]);
        $meja = $idmeja ? Table::find($idmeja) : null; // Kirim model meja
        $products = Product::all();
        $tables = Table::all(); 

        return view('customer.menu', compact('meja', 'products','tables'));
    }

    public function index()
    {
        // Ambil data meja dari session
        $mejaId = session('meja_id');

        // Ambil semua produk dari database
        $meja = $mejaId ? Table::find($mejaId) : null; // Kirim model meja
        $products = Product::all();
        $tables = Table::all(); 


        // Kirim data ke view
        return view('customer.menu', compact('meja', 'products', 'tables'));
    }

    public function konfirmasiPembayaran(Request $request)
    {

        // dd($request);
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return response()->json([
                'redirect' => route('login')
            ], 401);
        }

        // Validasi input dari frontend
        $request->validate([
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.qty' => 'required|integer|min:1',
            'paymentMethod' => 'required|in:qris,cash',
            'idMeja' => 'required|exists:tables,id',
        ]);

        // Ambil data meja berdasarkan id
        $meja = Table::find($request->idMeja);
        if (!$meja) {
            return response()->json(['status' => 'error', 'message' => 'Meja tidak ditemukan.'], 400);
        }
// dd($meja->id);
        // Total harga transaksi
        $totalHarga = 0;
        $totalDiskon = 0;
        $totalBayar = 0;

        // Mulai transaksi penjualan (sales)
        DB::beginTransaction();
        try {
            // Simpan data penjualan (sales) dengan status_pesanan 'belum_diproses'
            $sale = Sale::create([
                'id_user' => Auth::id(),
                'id_meja' => $meja->id,
                'total_harga' => 0, // Akan dihitung setelah semua detail transaksi
                'total_diskon' => $totalDiskon,
                'total_bayar' => 0, // Akan dihitung setelah semua detail transaksi
                'metode_bayar' => $request->paymentMethod,
                'status_bayar' => 'pending', // Status sementara
                'status_pesanan' => 'belum_diproses', // Status pesanan baru dibuat
            ]);

            // Simpan detail transaksi (sales_details)
            foreach ($request->cart as $item) {
                // Ambil produk berdasarkan ID
                $produk = Product::find($item['id']);
                if (!$produk) {
                    throw new \Exception("Produk dengan ID {$item['id']} tidak ditemukan.");
                }

                // Validasi stok produk
                if ($produk->stok_produk < $item['qty']) {
                    throw new \Exception("Stok produk {$produk->nama_produk} tidak mencukupi.");
                }

                // Hitung subtotal dan total
                $subtotal = $produk->harga_produk * $item['qty'];
                $totalHarga += $subtotal;
                $totalBayar += $subtotal;

                // Simpan detail transaksi
                SaleDetail::create([
                    'id_sale' => $sale->id,
                    'id_produk' => $produk->id,
                    'nama_produk' => $produk->nama_produk,
                    'harga_produk' => $produk->harga_produk,
                    'jumlah' => $item['qty'],
                    'subtotal' => $subtotal,
                    'diskon' => 0, // Misalnya diskon 0, bisa diubah sesuai aturan
                    'total' => $subtotal,
                ]);

                // Kurangi stok produk
                $produk->stok_produk -= $item['qty'];
                $produk->save();
            }

            // Update total harga dan bayar
            $sale->total_harga = $totalHarga;
            $sale->total_bayar = $totalBayar;
            $sale->status_bayar = 'lunas'; // Set status bayar menjadi lunas
            $sale->status_pesanan = 'sedang_diproses'; // Status pesanan saat pembayaran diproses
            $sale->save();

            // Commit transaksi
            DB::commit();

            // Sukses
            session()->flash('success', 'Pesanan berhasil diproses!');
            return response()->json([
                'status' => 'success',
                'reload' => true
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }


    
    

    
}
