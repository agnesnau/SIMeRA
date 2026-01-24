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
        'nama_lengkap',
        'username',
        'password',
        'level',
        'nip',
        'foto', 
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