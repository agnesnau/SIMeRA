<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RetentionAction; // Wajib panggil Model aktivitasnya!

class AuditTrailController extends Controller
{
    public function index()
    {
        // Mengambil semua data aktivitas retensi, diurutkan dari yang terbaru, 
        // dan dibuat berhalaman (paginate) 15 baris per halaman.
        // Kita juga menyertakan relasi 'user' dan 'patient' seperti di Dashboard.
        $logs = RetentionAction::with(['user', 'patient'])
                    ->latest() 
                    ->paginate(15);

        return view('audit_trail.index', compact('logs'));
    }
}