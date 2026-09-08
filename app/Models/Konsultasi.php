<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Konsultasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'perusahaan_id',
        'topik',
        'lokasi_diajukan',
        'waktu_diajukan',
        'lokasi_disetujui',
        'waktu_disetujui',
        'catatan_admin',
        'status',
    ];

    protected $casts = [
        'waktu_diajukan' => 'datetime',
        'waktu_disetujui' => 'datetime',
    ];

    // Relasi dengan model Perusahaan
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }
}
