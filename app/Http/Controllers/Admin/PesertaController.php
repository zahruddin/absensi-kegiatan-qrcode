<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; // <-- Kita akan bekerja dengan model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class PesertaController extends Controller
{
    /**
     * Menampilkan halaman daftar peserta (user dengan role 'peserta').
     * URL: /admin/peserta
     * Nama Route: admin.peserta.index
     */
    public function index()
    {
        $pesertas = User::where('role', 'peserta')->latest()->paginate(10);
        return view('admin.peserta.index', compact('pesertas'));
    }

    /**
     * Menyimpan data peserta baru ke dalam tabel users.
     * URL: /admin/peserta/store
     * Nama Route: admin.peserta.store
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'peserta', // Role di-set otomatis menjadi 'peserta'
        ]);

        return redirect()->route('admin.peserta.index')->with('success', 'Akun peserta baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data peserta.
     * URL: /admin/peserta/update/{user}
     * Nama Route: admin.peserta.update
     */
    public function update(Request $request, User $user)
    {
        // Lapisan keamanan: pastikan kita hanya mengedit user dengan role 'peserta'
        if ($user->role !== 'peserta') {
            abort(404); // Tampilkan Not Found jika mencoba edit admin/operator
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = $request->except('password');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.peserta.index')->with('success', 'Data peserta berhasil diperbarui.');
    }

    /**
     * Menghapus data peserta.
     * URL: /admin/peserta/destroy/{user}
     * Nama Route: admin.peserta.destroy
     */
    public function destroy(User $user)
    {
        // Lapisan keamanan: pastikan kita hanya menghapus user dengan role 'peserta'
        if ($user->role !== 'peserta') {
            abort(404);
        }
        
        $user->delete();
        return redirect()->route('admin.peserta.index')->with('success', 'Akun peserta berhasil dihapus.');
    }
}
