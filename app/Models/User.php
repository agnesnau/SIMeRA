<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * DAFTAR KOLOM YANG BOLEH DIISI (Sangat Penting!)
     * Jika kolom baru tidak ada di sini, Seeder akan gagal menyimpan data.
     */
    protected $fillable = [
        'nip',            // <--- Pastikan ini ada
        'nama_lengkap',   // <--- Pastikan ini ada
        'username',       // <--- Pastikan ini ada
        'email',
        'password',
        'level',          // <--- Pastikan ini ada
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}