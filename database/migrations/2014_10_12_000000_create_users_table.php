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
        Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('nip', 20)->nullable()->unique(); // Nomor Induk Pegawai
                $table->string('username', 50)->unique();
                $table->string('password');
                $table->string('nama_lengkap', 100);
                $table->enum('level', ['admin', 'petugas', 'supervisor'])->default('petugas');
                $table->rememberToken();
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
