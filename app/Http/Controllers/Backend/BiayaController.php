<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Biaya;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;

class BiayaController extends Controller
{
    /**
     * Menampilkan daftar biaya transportasi dengan fitur pencarian dan filter kategori.
     */
    public function index(Request $request): View
    {
        $this->checkAuthorization(auth()->user(), ['biaya.view']);

        $query = Biaya::query();

        // Filter berdasarkan kategori jika ada
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter berdasarkan pencarian jenis kendaraan jika ada
        if ($request->filled('search')) {
            $query->where('jenisKendaraan', 'like', '%' . $request->search . '%');
        }

        // Ambil data terbaru dan lakukan paginasi
        $biayas = $query->latest()->paginate(10)->withQueryString();

        return view('backend.pages.biaya.index', compact('biayas'));
    }

    /**
     * Menampilkan form untuk membuat data biaya transportasi baru.
     */
    public function create(): View
    {
        $this->checkAuthorization(auth()->user(), ['biaya.create']);
        return view('backend.pages.biaya.create');
    }

    /**
     * Menyimpan data biaya transportasi baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['biaya.create']);

        // Validasi input data
        $validated = $request->validate([
            'kategori'       => 'required|string|max:100',
            'jenisKendaraan' => 'required|string|max:100|unique:biayas,jenisKendaraan',
            'factorEmisi'    => 'required|numeric|min:0',
        ]);

        // Buat entri baru di database
        Biaya::create($validated);

        return redirect()->route('admin.biaya.index')
            ->with('success', 'Data biaya transportasi berhasil ditambahkan.');
    }



    ## Metode dengan `findOrFail` Eksplisit

    ### `show`

    /**
     * Menampilkan detail biaya transportasi tertentu.
     */
    public function show(int $id): View
    {
        $this->checkAuthorization(auth()->user(), ['biaya.view']);

        // Menggunakan findOrFail secara eksplisit
        $biaya = Biaya::findOrFail($id);

        return view('backend.pages.biaya.show', compact('biaya'));
    }

    ### `edit`

    /**
     * Menampilkan form untuk mengedit data biaya transportasi tertentu.
     */
    public function edit(int $id): View
    {
        $this->checkAuthorization(auth()->user(), ['biaya.edit']);

        // Menggunakan findOrFail secara eksplisit
        $biaya = Biaya::findOrFail($id);
        $kategori = $biaya -> kategori;
        return view('backend.pages.biaya.edit', compact('biaya','kategori'));
    }

    ### `update`

    /**
     * Memperbarui data biaya transportasi tertentu di database.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['biaya.edit']);

        // Menggunakan findOrFail secara eksplisit
        $biaya = Biaya::findOrFail($id);

        // Validasi input data
        $validated = $request->validate([
            'kategori'       => 'required|string|max:255',
            'jenisKendaraan' => [
                'required',
                'string',
                'max:255',
                // Pastikan jenis kendaraan unik kecuali untuk data yang sedang diedit
                Rule::unique('biayas', 'jenisKendaraan')->ignore($biaya->id),
            ],
            'factorEmisi'    => 'required|numeric|min:0',
        ]);

        // Perbarui entri di database
        $biaya->update($validated);

        return redirect()->route('admin.biaya.index')
            ->with('success', 'Data biaya transportasi berhasil diperbarui.');
    }

    ### `destroy`

    /**
     * Menghapus data biaya transportasi tertentu dari storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['biaya.delete']);

        // Menggunakan findOrFail secara eksplisit
        $biaya = Biaya::findOrFail($id);

        $biaya->delete();

        return redirect()->route('admin.biaya.index')
            ->with('success', 'Data biaya transportasi berhasil dihapus.');
    }
}
