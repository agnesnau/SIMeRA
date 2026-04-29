<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            // Menambah kolom status_approval, default 0 (Belum di-ACC)
            // diletakkan setelah kolom manual_status biar rapi di database
            $table->tinyInteger('status_approval')->default(0)->after('manual_status');
        });
    }

    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('status_approval');
        });
    }
};