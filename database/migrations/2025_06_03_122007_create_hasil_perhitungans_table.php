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
        Schema::create('hasil_perhitungans', function (Blueprint $table) {
            $table->id();

            $table->string('metode')->nullable(); // Tambahkan kolom metode

            $table->foreignId('transportasi_id')->nullable()->constrained('transportasis')->onDelete('set null');

            // Relasi ke bahan bakar (jika pakai metode bahan bakar)
            $table->foreignId('bahan_bakar_id')->nullable()->constrained('bahan_bakars')->onDelete('set null');

            // Relasi ke biaya (jika pakai metode biaya)
            $table->foreignId('biaya_id')->nullable()->constrained('biayas')->onDelete('set null');

            // Input dari user: liter, kilometer, atau rupiah
            $table->decimal('nilai_input', 12, 2);

            // Jumlah orang (jika ada)
            $table->integer('jumlah_orang')->nullable();

            // Hasil akhir perhitungan emisi (kg CO2)
            $table->decimal('hasil_emisi', 12, 4);

            // Tanggal perjalanan
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_perhitungans');
    }
};
