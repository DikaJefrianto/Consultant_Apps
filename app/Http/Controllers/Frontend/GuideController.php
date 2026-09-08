<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Guide; // Pastikan namespace ini benar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Tambahkan ini untuk debugging

class GuideController extends Controller
{
    /**
     * Menampilkan daftar panduan di landing page dalam format tabel.
     */
    public function index()
    {
        try {
            // Mengambil semua panduan. Kolom 'is_active' dan 'order' telah dihapus.
            // Data akan diambil berdasarkan ID secara default.
            $guides = Guide::all(); // Ini akan mengembalikan Collection, bahkan jika kosong

            // Log untuk debugging: cek apakah $guides terdefinisi dan berisi data
            Log::info('Guides data loaded: ' . $guides->count() . ' items.');

            // Kirim data panduan ke view
            return view('frontend.guides.index', compact('guides'));
        } catch (\Exception $e) {
            // Log error jika ada masalah saat mengambil data dari database
            Log::error('Error in Frontend\GuideController@index: ' . $e->getMessage());

            // return view('error.page', ['message' => 'Gagal memuat panduan.']);
            // Untuk sementara, kita bisa lempar kembali error agar terlihat
            throw $e;
        }
    }
}
