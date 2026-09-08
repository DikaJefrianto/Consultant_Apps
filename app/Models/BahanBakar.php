<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanBakar extends Model
{
    use HasFactory;

    protected $table = 'bahan_bakars';

    protected $fillable = [
        'user_id',
        'kategori',
        'Bahan_bakar',
        'factorEmisi'
    ];

    public function kendaraans()
    {
        return $this->hasMany(Kendaraan::class);
    }
}
