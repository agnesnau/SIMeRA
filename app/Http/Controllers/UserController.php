<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // 1. TAMPILKAN DATA
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    // 2. FORM TAMBAH
    public function create()
    {
        return view('users.create');
    }

    // 3. SIMPAN DATA BARU
    public function store(Request $request)
    {
        $request->validate([
            'nip'          => 'required|string|unique:users,nip',
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|unique:users,username', // Pakai 'string', bukan 'username'
            'password'     => 'required|min:6',
            'level'        => 'required|in:admin,petugas,kepala_puskesmas',
        ]);

        User::create([
            'nip'          => $request->nip,
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'level'        => $request->level,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan!');
    }

    // 4. FORM EDIT
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    // 5. UPDATE DATA
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            // Validasi Unique kecuali punya diri sendiri (.$id)
            'nip'          => 'required|string|unique:users,nip,'.$id,
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|unique:users,username,'.$id,
            'level'        => 'required|in:admin,petugas,kepala_puskesmas',
        ]);

        $data = [
            'nip'          => $request->nip,
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'level'        => $request->level,
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui!');
    }

    // 6. HAPUS DATA
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }

    public function show($id)
    {
        // 1. Cari user berdasarkan ID
        $user = \App\Models\User::findOrFail($id);

        // 2. Tampilkan ke halaman detail
        // Pastikan nanti kamu buat file view-nya di resources/views/users/show.blade.php
        return view('users.show', compact('user'));
    }
}

