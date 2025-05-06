<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\Product;

class ProdukController extends Controller
{
    //
    public function index() 
    {

        // Ambil produk hanya dari outlet yang dipilih
        $products = Product::paginate(10);


        // Kirim data ke view
        return view('admin.kelolaproducts', compact('products'));
    }
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_produk'  => 'required|string|max:255',
            'harga_produk' => 'required|numeric|min:0',
            'stok_produk'  => 'required|integer|min:0',
            'deskripsi'    => 'nullable|string',
            'gambar'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'       => 'required|in:aktif,nonaktif',
        ]);

        // Proses upload gambar ke public/assets/gambar_produk/
        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $namaFile = time() . '_' . $file->getClientOriginalName();
        
            // Simpan file ke `storage/app/public/gambar_produk/`
            $gambarPath = $file->storeAs('public/gambar_produk', $namaFile);
        
            // Ubah path agar bisa diakses melalui `public/storage/gambar_produk/`
            $gambarPath = str_replace('public/', 'storage/', $gambarPath);
        }        

        // Simpan data produk ke database
        Product::create([
            'nama_produk'  => $request->nama_produk,
            'harga_produk' => $request->harga_produk,
            'stok_produk'  => $request->stok_produk,
            'deskripsi'    => $request->deskripsi,
            'gambar'       => $gambarPath,
            'status'       => $request->status,
        ]);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan!');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga_produk' => 'required|numeric|min:0',
            'stok_produk' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $produk = Product::findOrFail($id);

        // Simpan gambar baru jika ada
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($produk->gambar && file_exists(public_path($produk->gambar))) {
                unlink(public_path($produk->gambar));
            }

            // Simpan gambar baru ke `storage/app/public/gambar_produk/`
            $file = $request->file('gambar');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/gambar_produk', $namaFile);

            // Path gambar untuk disimpan di database (agar bisa diakses via `storage/`)
            $gambarPath = "storage/gambar_produk/" . $namaFile;

            // Perbarui gambar di database
            $produk->gambar = $gambarPath;
        }

        // Update data produk tanpa mengganti gambar jika tidak ada gambar baru
        $produk->update([
            'nama_produk' => $request->nama_produk,
            'harga_produk' => $request->harga_produk,
            'stok_produk' => $request->stok_produk,
            'deskripsi' => $request->deskripsi,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Produk berhasil diperbarui.');
    }

    public function delete($id)
    {
        try {
            // Cari produk berdasarkan id_outlet dan id
            $product = Product::where('id', $id)->firstOrFail();
    
            // Hapus produk
            $product->delete();
            session()->flash('success', 'Product berhasil dihapus!');
            return response()->json(['success' => 'Produk berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
