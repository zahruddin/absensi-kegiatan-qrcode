<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peserta;
use App\Models\Kegiatan;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel; 
use App\Imports\PesertaImport; 
use App\Exports\PesertaExport;       
use App\Exports\PesertaLinkQrExport;


class PesertaController extends Controller
{
    /**
     * Simpan peserta baru.
     */
    public function store(Request $request)
    {
        // 1. Validasi input dari form
        $request->validate([
            'id_kegiatan' => 'required|exists:kegiatan,id',
            'nama'        => 'required|string|max:255',
            'email'       => 'nullable|email',
            'no_hp'       => 'nullable|string|max:20',
            'prodi'       => 'nullable|string|max:100',
            'nim'         => 'nullable|string|max:50',
            'kelompok'    => 'nullable|string|max:50',
        ]);

        // 2. Generate token unik yang akan menjadi isi QR Code
        $uniqueToken = Str::random(40);

        // ✅ DIUBAH: Nama file sekarang konsisten, menggunakan bagian dari token
        $safeName = Str::slug($request->nama);             // Mengubah "Budi Santoso" -> "budi-santoso"
        $uniquePart = substr($uniqueToken, 0, 8);          // Ambil 8 karakter pertama dari token baru
        $fileName = 'qrcode_' . $safeName . '_' . $uniquePart . '.png';

        // Path penyimpanan di dalam folder public/storage
        $filePath = 'qrcode_peserta/' . $fileName;

        // 3. Generate gambar QR Code dengan konten dari token
        $qrCodeImage = QrCode::format('png')
            ->size(300)
            ->margin(2)
            ->generate($uniqueToken);

        // Simpan file gambar QR Code ke disk 'public'
        Storage::disk('public')->put($filePath, $qrCodeImage);

        // 4. Simpan data lengkap peserta ke database
        Peserta::create([
            'id_kegiatan' => $request->id_kegiatan,
            'nama'        => $request->nama,
            'email'       => $request->email,
            'no_hp'       => $request->no_hp,
            'prodi'       => $request->prodi,
            'nim'         => $request->nim,
            'kelompok'    => $request->kelompok,
            'qrcode'      => $filePath,
            'token'       => $uniqueToken,
        ]);

        return redirect()->back()->with('success', 'Peserta berhasil ditambahkan dengan QR Code.');
    }

    public function update(Request $request, $id)
    {
        // Cek 1: Lihat semua data yang dikirim dari form
        // dd($request->all()); 

        $peserta = Peserta::findOrFail($id);

        $validatedData = $request->validate([
            'nama'     => 'required|string|max:250',
            'nim'      => 'nullable|string|max:250',
            'email'    => 'nullable|email|max:250',
            'no_hp'    => 'nullable|string|max:250',
            'prodi'    => 'nullable|string|max:250',
            'kelompok' => 'nullable|string|max:250',
        ]);

        // Cek 2: Lihat data setelah divalidasi
        // dd($validatedData);

        $peserta->update($validatedData);

        return redirect()->back()->with('success', 'Data peserta berhasil diperbarui.');
    }

    public function delete($id)
    {
        $peserta = Peserta::findOrFail($id);

        // Hapus juga file QR jika ada
        if ($peserta->qrcode && Storage::disk('public')->exists($peserta->qrcode)) {
            Storage::disk('public')->delete($peserta->qrcode);
        }

        $peserta->delete();

        return redirect()->back()->with('success', 'Peserta berhasil dihapus.');
    }
    public function import(Request $request)
    {
        // 3. Validasi request dari form modal
        $request->validate([
            'id_kegiatan' => 'required|exists:kegiatan,id',
            'file'        => 'required|mimes:xlsx,xls', // Pastikan file adalah Excel
        ]);

        try {
            // 4. Panggil class import, kirim id_kegiatan & file
            Excel::import(new PesertaImport($request->id_kegiatan), $request->file('file'));
            
            // 5. Jika sukses, redirect kembali dengan pesan
            return redirect()->back()->with('success', 'Data peserta berhasil diimpor!');

        } catch (\Exception $e) {
            // 6. Jika terjadi error, tangkap dan kembalikan dengan pesan error
            return redirect()->back()->with('error', 'Gagal mengimpor data. Pastikan format file Excel sudah benar. Error: ' . $e->getMessage());
        }
    }
    
    public function export(Kegiatan $kegiatan)
    {
        // 4. Buat nama file yang dinamis dan mudah dikenali
        // Contoh: 'peserta-seminar-nasional-2025.xlsx'
        $fileName = 'peserta-' . Str::slug($kegiatan->nama) . '.xlsx';
        
        // 5. Panggil library Excel untuk men-download file
        // Kita membuat instance baru dari PesertaExport dan mengirimkan objek $kegiatan
        return Excel::download(new PesertaExport($kegiatan), $fileName);
    }
    public function exportLinkQr(Kegiatan $kegiatan)
    {
        // 2. Buat nama file yang dinamis
        $fileName = 'link-qrcode-peserta-' . Str::slug($kegiatan->nama) . '.xlsx';
        
        // 3. Panggil library Excel untuk men-download file
        return Excel::download(new PesertaLinkQrExport($kegiatan), $fileName);
    }
    public function downloadQRCode($id)
    {
        $peserta = Peserta::findOrFail($id);

        if (!$peserta->qrcode || !Storage::disk('public')->exists($peserta->qrcode)) {
            return redirect()->back()->with('error', 'QR Code tidak ditemukan.');
        }

        // Download file QR
        return Storage::disk('public')->download($peserta->qrcode, $peserta->nama.'_qrcode.png');
    }

}
