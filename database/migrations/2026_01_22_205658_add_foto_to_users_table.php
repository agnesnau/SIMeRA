<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Tambahkan kolom foto setelah kolom email atau password
        // Gunakan nullable() supaya user lama yang belum punya foto tidak error
        $table->string('foto')->nullable()->after('password');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('foto');
    });
}
};
