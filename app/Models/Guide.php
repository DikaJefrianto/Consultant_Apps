<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Guide extends Model
{
    use HasFactory;

    // Atribut yang dapat diisi secara massal (mass assignable)
    protected $fillable = [
        'title',
        'category',
        'description',
        'file_path',
        'thumbnail_path',
        'thumbnail_alt',
        // 'file_name', 'link_url', 'order', 'is_active' dihapus dari fillable
    ];

    // Accessor untuk mendapatkan URL publik dari file panduan
    public function getFileUrlAttribute()
    {
        // Pastikan $this->file_path berisi path relatif dari storage/app/public
        // Contoh: 'guides/files/contoh.pdf'
        if ($this->file_path) {
            // Storage::url() akan menghasilkan URL lengkap yang mengarah ke file
            return Storage::url($this->file_path);
            // Alternatif: return asset('storage/' . $this->file_path);
        }
        return null;
    }

    // Accessor untuk mendapatkan URL publik dari thumbnail gambar (digunakan di backend jika ada)
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_path ? Storage::url($this->thumbnail_path) : null;
    }
}
