<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Cek dulu sebelum menambah kolom manual_status
            if (!Schema::hasColumn('patients', 'manual_status')) {
                $table->string('manual_status')->nullable()->after('nik');
            }
            
            // Cek dulu sebelum menambah kolom nilai_guna_path
            if (!Schema::hasColumn('patients', 'nilai_guna_path')) {
                $table->string('nilai_guna_path')->nullable()->after('manual_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['manual_status', 'nilai_guna_path']);
        });
    }
};