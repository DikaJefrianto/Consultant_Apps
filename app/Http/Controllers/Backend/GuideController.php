<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GuideController extends Controller
{
    /**
     * Menampilkan daftar panduan di halaman manajemen dashboard.
     */
    public function index()
    {
        $this->checkAuthorization(auth()->user(), ['guide.view']);
        // Mengambil semua panduan, tidak ada lagi 'order' atau 'is_active' untuk filtering/ordering
        $guides = Guide::paginate(10);
        return view('backend.pages.guides.index', compact('guides'));
    }

    /**
     * Menampilkan form untuk membuat panduan baru.
     */
    public function create()
    {
        $this->checkAuthorization(auth()->user(), ['guide.create']);
        return view('backend.pages.guides.create');
    }

    /**
     * Menyimpan panduan baru ke database, termasuk upload file dan thumbnail.
     */
    public function store(Request $request)
    {
        $this->checkAuthorization(auth()->user(), ['guide.create']);
        // Validasi input dari form
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120', // Max 5MB
            'thumbnail' => 'nullable|image|max:2048', // Max 2MB
            'thumbnail_alt' => 'nullable|string|max:255',
            // Validasi untuk 'link_url', 'order', 'is_active' dihapus
        ]);

        $filePath = null;
        // 'file_name' telah dihapus dari tabel, jadi tidak perlu disimpan nama asli file
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('guides/files', 'public');
        }

        $thumbnailPath = null;
        $thumbnailAlt = $request->input('thumbnail_alt') ?? $request->input('title'); // Menggunakan judul jika alt kosong
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailPath = $thumbnail->store('guides/thumbnails', 'public');
        }

        Guide::create([
            'title' => $request->title,
            'category' => $request->category,
            'description' => $request->description,
            'file_path' => $filePath,
            'thumbnail_path' => $thumbnailPath,
            'thumbnail_alt' => $thumbnailAlt,

        ]);

        return redirect()->route('admin.guides.index')->with('success', 'Panduan berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit panduan yang sudah ada.
     */
    public function edit(Guide $guide)
    {
        $this->checkAuthorization(auth()->user(), ['guide.edit']);
        return view('backend.pages.guides.edit', compact('guide'));
    }

    /**
     * Memperbarui panduan di database, termasuk penggantian file dan thumbnail.
     */
    public function update(Request $request, Guide $guide)
    {
        $this->checkAuthorization(auth()->user(), ['guide.edit']);
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
            'thumbnail' => 'nullable|image|max:2048',
            'thumbnail_alt' => 'nullable|string|max:255',
            // Validasi untuk 'link_url', 'order', 'is_active' dihapus
        ]);

        // Ambil data yang akan diupdate
        $data = $request->only(['title', 'category', 'description', 'thumbnail_alt']);
        $data['thumbnail_alt'] = $request->input('thumbnail_alt') ?? $request->input('title');


        // Proses penggantian file panduan
        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($guide->file_path) {
                Storage::disk('public')->delete($guide->file_path);
            }
            $file = $request->file('file');
            $data['file_path'] = $file->store('guides/files', 'public');
        }

        // Proses penggantian thumbnail
        if ($request->hasFile('thumbnail')) {
            // Hapus thumbnail lama jika ada
            if ($guide->thumbnail_path) {
                Storage::disk('public')->delete($guide->thumbnail_path);
            }
            $thumbnail = $request->file('thumbnail');
            $data['thumbnail_path'] = $thumbnail->store('guides/thumbnails', 'public');
        }

        $guide->update($data);

        return redirect()->route('admin.guides.index')->with('success', 'Panduan berhasil diperbarui!');
    }

    /**
     * Menghapus panduan dari database, termasuk file terkait.
     */
    public function destroy(Guide $guide)
    {
        $this->checkAuthorization(auth()->user(), ['guide.delete']);
        // Hapus file panduan dari storage jika ada
        if ($guide->file_path) {
            Storage::disk('public')->delete($guide->file_path);
        }
        // Hapus thumbnail dari storage jika ada
        if ($guide->thumbnail_path) {
            Storage::disk('public')->delete($guide->thumbnail_path);
        }

        // Hapus entri dari database
        $guide->delete();

        return redirect()->route('admin.guides.index')->with('success', 'Panduan berhasil dihapus!');
    }
}
