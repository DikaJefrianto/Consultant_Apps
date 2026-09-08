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
        Schema::table('hasil_perhitungans', function (Blueprint $table) {
            $table->string('titik_awal')->nullable()->before('kategori');
            $table->string('titik_tujuan')->nullable()->after('titik_awal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_perhitungans', function (Blueprint $table) {
            $table->dropColumn(['titik_awal', 'titik_tujuan']);
        });
    }
};
