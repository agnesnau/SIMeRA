<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // 1. MIDDLEWARE: Pengaman Akses
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $routeId = $request->route('id'); // Ambil ID dari URL

            // IZINKAN jika dia Admin (atau Supervisor jika boleh kelola user)
            if (in_array($user->level, ['admin', 'supervisor'])) {
                return $next($request);
            }

            // IZINKAN jika dia ingin mengedit/update profilnya SENDIRI
            if (in_array($request->route()->getName(), ['users.edit', 'users.update', 'users.show'])) {
                if ($user->id == $routeId) {
                    return $next($request);
                }
            }

            return redirect('dashboard')->with('error', 'Otoritas Ditolak! Anda tidak memiliki izin mengelola akun ini.');
        });
    }

    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        // FIX: Tambahkan 'supervisor' di validasi
        $request->validate([
            'nip'          => 'required|unique:users,nip',
            'nama_lengkap' => 'required|max:255',
            'username'     => 'required|unique:users,username',
            'password'     => 'required|min:6',
            'level'        => 'required|in:admin,petugas,supervisor', 
        ]);

        User::create([
            'nip'          => $request->nip,
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'level'        => $request->level,
        ]);

        return redirect()->route('users.index')->with('success', 'User Baru Berhasil Didaftarkan!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    // --- PERBAIKAN UTAMA DI SINI ---
    public function update(Request $request, $id) 
    {
        $user = User::findOrFail($id);

        // 1. Validasi Input (Pastikan level 'supervisor' diterima)
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username,' . $user->id,
            'nip'          => 'nullable|string|max:20',
            'level'        => 'required|in:admin,petugas,supervisor', // Wajib ada
            'foto'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // 2. Ambil data (Langsung ambil 'level' juga biar tidak ketinggalan)
        $data = $request->only(['nama_lengkap', 'username', 'nip', 'level']);

        // 3. Cek Foto
        if ($request->hasFile('foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $data['foto'] = $request->file('foto')->store('profil', 'public');
        }

        // 4. Update Password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // 5. Eksekusi Update
        $user->update($data);

        // 6. Redirect
        // Jika edit profil sendiri dan bukan admin, balik ke dashboard
        if (auth()->user()->id == $id && auth()->user()->level !== 'admin') {
            return redirect()->route('dashboard')->with('success', 'Profil SIMeRA Anda Berhasil Diperbarui!');
        }
        
        return redirect()->route('users.index')->with('success', 'Data User Berhasil Diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Akun Pengguna Berhasil Dihapus!');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }
}