<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Strategi extends Model
{
    use HasFactory;

    protected $fillable = ['nama_program','deskripsi','dokumen', 'status','perusahaan_id']; //isi tabel strategi

    // hubungkan perusahaan dengan strategi
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }
}
