<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        // Cek jika pengguna sudah login
        if (auth()->check()) {
            // Jika sudah login, arahkan ke halaman yang sesuai berdasarkan role
            $role = auth()->user()->role;
            // Misalnya jika role adalah admin, arahkan ke dashboard admin
            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            // Jika role adalah kasir, arahkan ke dashboard kasir
            if ($role === 'kasir') {
                return redirect()->route('kasir.dashboard');
            }
            if ($role === 'kasir') {
                return redirect()->route('customer.menu');
            }
            // Jika role tidak sesuai, bisa arahkan ke halaman utama atau halaman lain
            return redirect('/');
        }
        // Jika belum login, tampilkan halaman login
        return view('auth.login');
    }


    // Proses login
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username_or_email' => 'required|string',
            'password' => 'required|string',
        ]);

        $remember = $request->has('remember'); // Ambil status remember me

        // Cari user berdasarkan username atau email
        $user = User::where('username', $request->username_or_email)
                    ->orWhere('email', $request->username_or_email)
                    ->first();

        // Jika user ditemukan, coba login
        if ($user && Auth::attempt(['email' => $user->email, 'password' => $request->password], $remember)) {
            // Redirect berdasarkan role user
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'kasir':
                    return redirect()->route('kasir.sales');
                case 'customer':
                    return redirect()->route('customer.menu');
                default:
                    return redirect()->route('login')->withErrors(['message' => 'Role tidak dikenali.']);
            }
        }

        // Jika login gagal
        return back()->withErrors(['message' => 'Email/Username atau Password salah!'])->withInput();
    }


    
    public function logout(Request $request)
    {
        // Hapus remember_token dari database
        if (Auth::check()) {
            $user = Auth::user();
            $user->remember_token = null;
            $user->save();
        }
    
        Auth::logout(); // Logout pengguna
    
        $request->session()->invalidate(); // Hapus sesi
        $request->session()->regenerateToken(); // Regenerasi token CSRF
    
        return redirect('/login');
    }
    

}
