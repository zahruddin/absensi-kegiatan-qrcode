<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peserta;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;


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
            'email'       => 'email',
            'no_hp'       => 'string|max:20',
            'prodi'       => 'required|string|max:100',
            'nim'         => 'required|string|max:50',
            'kelompok'    => 'nullable|string|max:50',
        ]);

        // Generate string unik untuk QR Code
        $qrCodeString = Str::uuid()->toString();

        // Nama file QR Code
        $fileName = 'qrcode_' . $request->nim . '_' . time() . '.png';

        // Path penyimpanan
        $filePath = 'qrcode_peserta/' . $fileName;

        // Simpan QR Code ke storage (public)
        $qrCodeImage = QrCode::format('png')
            ->size(300)
            ->margin(2)
            ->generate($qrCodeString);

        Storage::disk('public')->put($filePath, $qrCodeImage);

        // Simpan ke database
        Peserta::create([
            'id_kegiatan' => $request->id_kegiatan,
            'nama'        => $request->nama,
            'email'       => $request->email,
            'no_hp'       => $request->no_hp,
            'prodi'       => $request->prodi,
            'nim'         => $request->nim,
            'kelompok'    => $request->kelompok,
            'qrcode'      => $filePath, // simpan path file, bukan string UUID
        ]);

        return redirect()->back()->with('success', 'Peserta berhasil ditambahkan dengan QR Code.');
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
