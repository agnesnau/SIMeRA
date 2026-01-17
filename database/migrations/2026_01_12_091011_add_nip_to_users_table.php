<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
        // Cek dulu: Kalau kolom 'nip' BELUM ada, baru tambahkan
        if (!Schema::hasColumn('users', 'nip')) {
            $table->string('nip')->unique()->nullable()->after('id');
    }});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nip');
        });
    }
}; 
// ^^^ PERHATIKAN: Wajib ada titik koma (;) di sini!