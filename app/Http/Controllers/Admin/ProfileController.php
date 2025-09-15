<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil admin.
     */
    public function index()
    {
        // Anda bisa mengambil data user yang login dan mengirimnya ke view
        $admin = auth()->user();
        return view('admin.profile', compact('admin'));
    }
}