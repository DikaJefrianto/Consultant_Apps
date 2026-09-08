<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi database.
     */
    public function up(): void
    {
        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Judul panduan
            $table->string('category')->nullable(); // Kategori panduan (misal: "Registrasi", "Penggunaan Fitur")
            $table->text('description')->nullable(); // Deskripsi singkat panduan
            $table->string('file_path')->nullable(); // Path ke file panduan (PDF, DOCX, dll.) di storage
            $table->string('thumbnail_path')->nullable(); // (Opsional) Path ke thumbnail gambar
            $table->string('thumbnail_alt')->nullable(); // (Opsional) Alt text untuk thumbnail
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Batalkan migrasi database.
     */
    public function down(): void
    {
        Schema::dropIfExists('guides');
    }
};

