<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Support\Facades\Auth;


class MenuController extends Controller
{
    //
    //
    public function scanQRCode($idmeja)
    {
        // Simpan data meja ke session
        session(['meja_id' => $idmeja]);

        // Redirect ke halaman menu atau login jika belum login
        if (Auth::check()) {
            return redirect()->route('customer.menu');  // jika sudah login
        } else {
            return redirect()->route('login');  // jika belum login
        }
    }

    public function index()
    {
        // Ambil data meja dari session
        $mejaId = session('meja_id');

        // Ambil semua produk dari database
        $meja = $mejaId ? Table::find($mejaId) : null; // Kirim model meja
        $products = Product::all();


        // Kirim data ke view
        return view('customer.menu', compact('meja', 'products'));
    }

    
}
