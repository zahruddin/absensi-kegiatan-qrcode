<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class OperatorController extends Controller
{
    /**
     * Menampilkan halaman daftar operator.
     */
    public function index()
    {
        $operators = User::where('role', 'operator')->latest()->paginate(10);
        // Pastikan view menunjuk ke folder yang benar
        return view('admin.operator', compact('operators'));
    }

    /**
     * Menyimpan operator baru.
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
            'role' => 'operator', // <-- Role di-set otomatis, sesuai permintaan Anda
        ]);

        return redirect()->route('admin.operator.index')->with('success', 'Operator baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data operator.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = $request->except('password');
        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.operator.index')->with('success', 'Data operator berhasil diperbarui.');
    }

    /**
     * Menghapus data operator.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.operator.index')->with('success', 'Operator berhasil dihapus.');
    }
}
