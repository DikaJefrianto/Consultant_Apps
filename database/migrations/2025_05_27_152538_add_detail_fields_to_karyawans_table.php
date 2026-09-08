<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->string('email')->nullable()->after('nama_lengkap');
            $table->string('no_hp')->nullable()->after('email');
            $table->string('alamat')->nullable()->after('no_hp');
            $table->string('jabatan')->nullable()->after('alamat');
        });
    }

    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropColumn(['email', 'no_hp', 'alamat', 'jabatan']);
        });
    }
};
