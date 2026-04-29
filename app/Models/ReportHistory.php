<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_surat', 'tanggal_ba', 'jenis_ba', 'tipe_dokumen', 'data_json'
    ];

    protected $casts = [
        'tanggal_ba' => 'date',
        'data_json' => 'array'
    ];
}