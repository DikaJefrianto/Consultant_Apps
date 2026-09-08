<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Transportasi;
use Illuminate\Http\Request;

class TransportasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkAuthorization(auth()->user(), ['transportasi.view']);
        $query = Transportasi::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $query->where('jenis', 'like', '%' . $request->search . '%');
        }

        $transportasis = $query->latest()->paginate(10)->withQueryString();

        return view('backend.pages.transportasi.index', compact('transportasis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkAuthorization(auth()->user(), ['transportasi.create']);
        return view('backend.pages.transportasi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkAuthorization(auth()->user(), ['transportasi.create']);
        $validated = $request->validate([
            'kategori' => 'required|string|max:100',
            'jenis' => 'required|string|max:100',
            'factor_emisi' => 'required|numeric|min:0',
        ]);

        Transportasi::create($validated);

        return redirect()->route('admin.transportasi.index')
            ->with('success', 'Data transportasi berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transportasi $transportasi)
    {
        $this->checkAuthorization(auth()->user(), ['transportasi.edit']);
        // $transportasi = Transportasi::findOrFail($transportasi);
        $kategori = $transportasi -> kategori;
        return view('backend.pages.transportasi.edit', compact('transportasi','kategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transportasi $transportasi)
    {
        $this->checkAuthorization(auth()->user(), ['transportasi.edit']);
        $validated = $request->validate([
            'kategori' => 'required|string|max:255',
            'jenis' => 'required|string|max:255',
            'factor_emisi' => 'required|numeric|min:0',
        ]);

        $transportasi->update($validated);

        return redirect()->route('admin.transportasi.index')
            ->with('success', 'Data transportasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transportasi $transportasi)
    {
        $this->checkAuthorization(auth()->user(), ['transportasi.delete']);
        $transportasi->delete();

        return redirect()->route('admin.transportasi.index')
            ->with('success', 'Data transportasi berhasil dihapus.');
    }
}
