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
        Schema::create('konsultasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->onDelete('cascade'); // ID perusahaan yang mengajukan
            $table->string('topik');
            $table->string('lokasi_diajukan')->nullable(); // Lokasi awal yang diajukan perusahaan
            $table->dateTime('waktu_diajukan')->nullable(); // Waktu awal yang diajukan perusahaan
            $table->string('lokasi_disetujui')->nullable(); // Lokasi yang disetujui admin
            $table->dateTime('waktu_disetujui')->nullable(); // Waktu yang disetujui admin
            $table->text('catatan_admin')->nullable(); // Catatan dari admin
            $table->enum('status', ['pending', 'ditolak', 'diterima'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konsultasis');
    }
};
