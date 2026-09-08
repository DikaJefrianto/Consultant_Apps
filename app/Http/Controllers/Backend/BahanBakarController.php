<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BahanBakar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule; // Pastikan Rule diimport

class BahanBakarController extends Controller
{
    /**
     * Menampilkan daftar bahan bakar dengan fitur pencarian dan filter kategori.
     */
    public function index(Request $request): View
    {
        $this->checkAuthorization(auth()->user(), ['bahanbakar.view']);

        $query = BahanBakar::query();

        // Filter berdasarkan kategori jika ada
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter berdasarkan pencarian nama bahan bakar jika ada
        if ($request->filled('search')) {
            $query->where('Bahan_bakar', 'like', '%' . $request->search . '%');
        }

        // Ambil data terbaru dan lakukan paginasi
        $bahan_bakars = $query->latest()->paginate(10)->withQueryString();

        return view('backend.pages.bahan-bakar.index', compact('bahan_bakars'));
    }

    /**
     * Menampilkan form untuk membuat data bahan bakar baru.
     */
    public function create(): View
    {
        $this->checkAuthorization(auth()->user(), ['bahanbakar.create']);
        return view('backend.pages.bahan-bakar.create');
    }

    /**
     * Menyimpan data bahan bakar baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['bahanbakar.create']);

        // Validasi input data
        $validated = $request->validate([
            'kategori'    => 'required|string|max:100',
            'Bahan_bakar' => 'required|string|max:100|unique:bahan_bakars,Bahan_bakar',
            'factorEmisi' => 'required|numeric|min:0',
        ]);

        // Buat entri baru di database
        BahanBakar::create($validated);

        return redirect()->route('admin.bahan-bakar.index')
            ->with('success', 'Data bahan bakar berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail bahan bakar tertentu.
     */
    public function show(int $id): View
    {
        $this->checkAuthorization(auth()->user(), ['bahanbakar.view']);

        // Menggunakan findOrFail secara eksplisit
        $bahanBakar = BahanBakar::findOrFail($id);

        return view('backend.pages.bahan-bakar.show', compact('bahanBakar'));
    }

    /**
     * Menampilkan form untuk mengedit bahan bakar tertentu.
     */
    public function edit(int $id): View
    {
        $this->checkAuthorization(auth()->user(), ['bahanbakar.edit']);

        // Menggunakan findOrFail secara eksplisit

        $bahanBakar = BahanBakar::findOrFail($id);
        $kategori = $bahanBakar->kategori;
        return view('backend.pages.bahan-bakar.edit', compact('bahanBakar','kategori'));
    }

    /**
     * Memperbarui data bahan bakar tertentu di database.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['bahanbakar.edit']);

        // Menggunakan findOrFail secara eksplisit
        $bahanBakar = BahanBakar::findOrFail($id);

        // Validasi input data
        $validated = $request->validate([
            'kategori'    => 'required|string|max:255',
            'Bahan_bakar' => [
                'required',
                'string',
                'max:255',
                // Pastikan nama bahan bakar unik kecuali untuk data yang sedang diedit
                Rule::unique('bahan_bakars', 'Bahan_bakar')->ignore($bahanBakar->id),
            ],
            'factorEmisi' => 'required|numeric|min:0',
        ]);

        // Perbarui entri di database
        $bahanBakar->update($validated);

        return redirect()->route('admin.bahan-bakar.index')
            ->with('success', 'Data bahan bakar berhasil diperbarui.');
    }

    /**
     * Menghapus bahan bakar tertentu dari database.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['bahanbakar.delete']);

        // Menggunakan findOrFail secara eksplisit
        $bahanBakar = BahanBakar::findOrFail($id);

        $bahanBakar->delete();

        return redirect()->route('admin.bahan-bakar.index')
            ->with('success', 'Data bahan bakar berhasil dihapus.');
    }
}
