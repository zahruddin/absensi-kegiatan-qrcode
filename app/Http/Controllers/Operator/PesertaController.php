<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peserta;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


class PesertaController extends Controller
{
    /**
     * Simpan peserta baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_kegiatan' => 'required|exists:kegiatan,id',
            'nama'        => 'required|string|max:255',
            'email'       => 'nullable|email',
            'no_hp'       => 'nullable|string|max:20',
            'prodi'       => 'nullable|string|max:100',
            'nim'         => 'nullable|string|max:50',
            'kelompok'    => 'nullable|string|max:50',
        ]);

        // 1. Generate token unik yang akan menjadi isi QR Code
        // Str::random() menghasilkan string acak yang lebih sulit ditebak daripada UUID
        $uniqueToken = Str::random(40);

        // Nama file QR Code agar unik
        $fileName = 'qrcode_' . $request->nim . '_' . time() . '.png';

        // Path penyimpanan di dalam folder public/storage
        $filePath = 'qrcode_peserta/' . $fileName;

        // Generate gambar QR Code dengan konten dari $uniqueToken
        $qrCodeImage = QrCode::format('png')
            ->size(300)
            ->margin(2)
            ->generate($uniqueToken); // <-- Gunakan token sebagai isi QR Code

        // Simpan file gambar QR Code ke disk 'public'
        Storage::disk('public')->put($filePath, $qrCodeImage);

        // 2. Simpan data peserta ke database
        Peserta::create([
            'id_kegiatan' => $request->id_kegiatan,
            'nama'        => $request->nama,
            'email'       => $request->email,
            'no_hp'       => $request->no_hp,
            'prodi'       => $request->prodi,
            'nim'         => $request->nim,
            'kelompok'    => $request->kelompok,
            'qrcode'      => $filePath,      // Simpan path file QR Code
            'token'       => $uniqueToken,   // Simpan token uniknya
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
