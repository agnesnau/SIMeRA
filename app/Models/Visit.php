<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal (Mass Assignment).
     * Pastikan 'poli_tujuan' dan 'diagnosa' ada di sini agar bisa disimpan ke DB.
     */
    protected $fillable = [
        'no_registrasi',
        'patient_id',
        'tgl_kunjungan',
        'poli_tujuan',
        'dokter',
        'diagnosa',
        'user_id'
    ];

    /**
     * Memastikan tgl_kunjungan otomatis dikonversi menjadi objek Carbon (Tanggal).
     * Ini mencegah error: "Call to a member function diffInYears() on string".
     */
    protected $casts = [
        'tgl_kunjungan' => 'date',
    ];

    /**
     * Relasi ke data Pasien.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relasi ke Petugas yang menginput.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}