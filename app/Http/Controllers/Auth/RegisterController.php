<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    /**
     * Menampilkan form registrasi.
     */
    public function showForm()
    {
        return view('auth.register');
    }

    /**
     * Menangani permintaan registrasi dan memvalidasinya.
     */
    public function register(Request $request)
    {
        // ✅ SEMUA VALIDASI DITERAPKAN DI SINI
        $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'username'   => ['required', 'string', 'max:255', 'unique:users'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
            
            // Validasi untuk Honeypot
            'fax_number' => ['prohibited'], 
            
            // Validasi untuk reCAPTCHA sudah dihapus
        ]);

        User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'peserta', // Role otomatis di-set menjadi 'peserta'
        ]);

        return redirect('/login')->with('success', 'Akun berhasil dibuat! Silakan login.');
    }
}