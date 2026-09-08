<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void{
    Schema::create('strategis', function (Blueprint $table) {
        $table->id();
        $table->string('nama_program');
        $table->text('deskripsi');
        $table->string('dokumen')->nullable(); // path dokumen upload
        $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
        $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strategis');
    }
};
