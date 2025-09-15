<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kegiatan;

class KegiatanController extends Controller
{
    //
    public function index()
    {
        // Ambil semua kegiatan dan hitung jumlah peserta & sesi
        $kegiatans = Kegiatan::withCount(['peserta', 'sesiAbsensi'])->latest()->paginate(10);
        return view('admin.kegiatan', compact('kegiatans'));
    }
}
