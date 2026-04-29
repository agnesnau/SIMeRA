<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_registrasi',
        'patient_id',
        'tgl_kunjungan',
        'poli_tujuan',
        'pembayaran',
        'user_id'
    ];
    
    protected $casts = [
        'tgl_kunjungan' => 'date',
    ];

    // Relasi balik ke Pasien
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Relasi ke User (Petugas yang input)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}