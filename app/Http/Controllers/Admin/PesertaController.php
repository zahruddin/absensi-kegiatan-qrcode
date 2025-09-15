<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peserta;
use App\Models\User;

class PesertaController extends Controller
{
    //
    public function index()
    {
        // Ambil semua peserta beserta relasi ke kegiatan dan user (jika ada)
        $pesertas = ;
        return view('admin.peserta', compact('pesertas'));
    }
}
