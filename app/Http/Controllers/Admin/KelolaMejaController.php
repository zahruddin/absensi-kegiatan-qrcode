<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KelolaMejaController extends Controller
{
    

    public function index() {
        $tables = Table::paginate(10);
        
        return view('admin.kelolameja' , compact('tables'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_meja' => 'required|string|max:255',
        ]);

        // Simpan data meja terlebih dahulu (tanpa QR code)
        $meja = Table::create([
            'nama_meja' => $request->nama_meja,
            'qr_code' => '', // Kosongkan dulu
        ]);

        // Buat nama file QR code berdasarkan ID
        $filename = 'qr_meja_' . $meja->id . '_' . time() . '.png';

        // Buat URL QR Code berdasarkan ID meja
        $baseUrl = config('app.url');
        $qrContent = $baseUrl . '/scan/' . $meja->id;

        // Buat QR code dengan margin putih
        $qrCode = QrCode::format('png')
                        ->size(300)
                        ->margin(2)
                        ->generate($qrContent);

        // Simpan QR code ke file
        $path = storage_path('app/public/qrcode');
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        file_put_contents($path . '/' . $filename, $qrCode);

        // Update data meja dengan path QR code
        $meja->update([
            'qr_code' => 'qrcode/' . $filename
        ]);

        return redirect()->back()->with('success', 'Meja dan QR Code berhasil dibuat!');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_meja'  => 'required|string|max:255',
            'status'       => 'required|in:ready,ordered',
        ]);

        $table = Table::findOrFail($id);

        // Update data produk tanpa mengganti gambar jika tidak ada gambar baru
        $table->update([
            'nama_meja'  => $request->nama_meja,
            'status'       => $request->status
        ]);

        return redirect()->back()->with('success', 'Meja berhasil diperbarui.');
    }

    public function delete($id)
    {
        try {
            // Cari meja berdasarkan id
            $table = Table::where('id', $id)->firstOrFail();
        
            // Ambil nama file QR code yang terkait dengan meja
            $qrCodeFile = storage_path('app/public/qrcode/' . basename($table->qr_code));
            
            // Jika file QR code ada, hapus file tersebut
            if (file_exists($qrCodeFile)) {
                unlink($qrCodeFile);
            }

            // Hapus meja dari database
            $table->delete();

            // Berikan notifikasi sukses
            session()->flash('success', 'Meja dan QR code berhasil dihapus!');
            return response()->json(['success' => 'Meja dan QR code berhasil dihapus!']);
        } catch (\Exception $e) {
            // Menangani kesalahan dan mengembalikan pesan error
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

}
