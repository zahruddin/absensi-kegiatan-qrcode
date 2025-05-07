<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class kelolaUserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10); // atau User::where('role', '!=', 'admin')->get() jika ingin exclude admin
        return view('admin.kelolauser', compact('users'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:admin,kasir,customer',
            'password' => 'required|string|min:6', // jika form-nya menyertakan password
        ]);

        // Simpan user baru
        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => bcrypt($validated['password']),
        ]);

        return redirect()->route('admin.kelolaUsers')->with('success', 'User berhasil ditambahkan');
    }

}

