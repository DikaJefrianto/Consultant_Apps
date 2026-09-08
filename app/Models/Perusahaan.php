<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Perusahaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'email',
        'alamat',
        'logo',
        'keterangan',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi lain jika ada, misal ke karyawan
    public function karyawans()
    {
        return $this->hasMany(Karyawan::class);
    }
    public function strategis()
    {
        return $this->hasMany(Strategi::class);
    }
    public function getAllRelatedUserIds()
    {
        // ID pemilik perusahaan (user yang terhubung langsung ke model Perusahaan)
        $ownerUserId = $this->user_id;

        // ID semua karyawan yang terhubung dengan perusahaan ini
        // Menggunakan pluck('user_id') untuk mendapatkan array dari user_id karyawan
        $employeeUserIds = $this->karyawans()->pluck('user_id')->toArray();

        // Gabungkan ID pemilik dan ID karyawan, lalu pastikan unik
        // array_filter digunakan untuk menghapus nilai null atau 0 jika ada
        return array_unique(array_filter(array_merge([$ownerUserId], $employeeUserIds)));
    }
}
