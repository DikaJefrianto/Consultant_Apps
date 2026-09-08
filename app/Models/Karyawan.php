<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawans';

    /**
     * Mass assignable attributes.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_lengkap',
        'no_hp',
        'alamat',
        'jabatan',
        'user_id',
        'perusahaan_id',
    ];

    /**
     * Relasi Karyawan ke User (satu karyawan punya satu user).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi Karyawan ke Perusahaan (satu karyawan bekerja di satu perusahaan).
     */
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    /**
     * Boot method to hook into model events.
     * Saat karyawan dihapus, user terkait juga dihapus.
     */
    protected static function booted()
    {
        static::deleting(function ($karyawan) {
            // Hapus user yang berelasi dengan karyawan ini
            if ($karyawan->user) {
                $karyawan->user->delete();
            }
        });
    }
}
