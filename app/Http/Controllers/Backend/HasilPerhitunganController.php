<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BahanBakar;
use App\Models\biaya;
use App\Models\HasilPerhitungan;
use App\Models\Karyawan;
use App\Models\transportasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HasilPerhitunganController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $this->checkAuthorization($user, ['perhitungan.view']);

        $bahanBakar = BahanBakar::all();
        $jenis      = Transportasi::all();
        $biayaList  = Biaya::all();

        // Default: query kosong tetapi tetap menggunakan paginate agar tidak error saat render view
        $perhitungan = HasilPerhitungan::whereRaw('1 = 0')->paginate(10);
        $totalEmisi  = 0;

        if ($user->hasRole('Karyawan')) {
            // Karyawan hanya bisa melihat data milik sendiri
            $perhitungan = HasilPerhitungan::with(['bahanBakar', 'transportasi', 'biaya', 'user'])
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(10);

            $totalEmisi = HasilPerhitungan::where('user_id', $user->id)->sum('hasil_emisi');
        } elseif ($user->hasRole('Perusahaan') && $user->perusahaan) {
            // Perusahaan hanya bisa melihat data dari semua karyawannya
            $userIds = Karyawan::where('perusahaan_id', $user->perusahaan->id)->pluck('user_id');

            $perhitungan = HasilPerhitungan::with(['bahanBakar', 'transportasi', 'biaya', 'user'])
                ->whereIn('user_id', $userIds)
                ->latest()
                ->paginate(10);

            $totalEmisi = HasilPerhitungan::whereIn('user_id', $userIds)->sum('hasil_emisi');
        } elseif ($user->hasRole(['Admin', 'Superadmin'])) {
            // Admin dan superadmin bisa melihat semua data
            $perhitungan = HasilPerhitungan::with(['bahanBakar', 'transportasi', 'biaya', 'user'])
                ->latest()
                ->paginate(10);

            $totalEmisi = HasilPerhitungan::sum('hasil_emisi');
        }

        return view('backend.pages.perhitungan.index', compact(
            'bahanBakar',
            'jenis',
            'biayaList',
            'perhitungan',
            'totalEmisi'
        ));
    }

    public function create(Request $request)
    {

        $this->checkAuthorization(auth()->user(), ['perhitungan.create']);
        $metodeOptions = [
            [
                'value' => 'bahan_bakar',
                'icon'  => 'bi-fuel-pump',
                'color' => 'text-black-600',
                'text'  => 'Bahan Bakar',                       // String asli
                'desc'  => 'Gunakan data konsumsi bahan bakar', // String asli
            ],
            [
                'value' => 'jarak_tempuh',
                'icon'  => 'bi-geo-alt-fill',
                'color' => 'text-black-600',
                'text'  => 'Jarak Tempuh',
                'desc'  => 'Gunakan data jarak perjalanan',
            ],
            [
                'value' => 'biaya',
                'icon'  => 'bi-cash-stack',
                'color' => 'text-black-600',
                'text'  => 'Biaya',
                'desc'  => 'Gunakan data biaya perjalanan',
            ],
        ];
        $kategori = $request->input('kategori');
        $metode   = $request->input('metode');

        $bahanBakar     = $kategori ? BahanBakar::where('kategori', $kategori)->get() : collect();
        $jenis          = $kategori ? Transportasi::where('kategori', $kategori)->get() : collect();
        $jenisKendaraan = $kategori ? Biaya::where('kategori', $kategori)->get() : collect();

        return view('backend.pages.perhitungan.create', compact(
            'bahanBakar',
            'jenis',
            'jenisKendaraan',
            'kategori',
            'metode',
            'metodeOptions'
        ));
    }

    public function store(Request $request)
    {
        $this->checkAuthorization(auth()->user(), ['perhitungan.create']);
        $validated = $request->validate([
            'kategori'     => 'required|string',
            'tanggal'      => 'required|date',
            'jumlah_orang' => 'nullable|numeric|min:1',
            'nilai_input'  => 'required|numeric|min:0',
            'metode'       => 'required|in:bahan_bakar,jarak_tempuh,biaya',
            'titik_awal'   => 'required|string|max:255',
            'titik_tujuan' => 'required|string|max:255',
        ]);

        $jumlah_orang = $validated['jumlah_orang'] ?? 1;
        $emisi        = 0;

        $data = array_merge($validated, [
            'user_id'      => auth()->id(),
            'jumlah_orang' => $jumlah_orang,
        ]);

        switch ($validated['metode']) {
            case 'bahan_bakar':
                $request->validate(['Bahan_bakar' => 'required|exists:bahan_bakars,id']);
                $bb                     = BahanBakar::findOrFail($request->Bahan_bakar);
                $data['bahan_bakar_id'] = $bb->id;
                $emisi                  = $bb->factorEmisi * $validated['nilai_input'] * $jumlah_orang;
                break;

            case 'jarak_tempuh':
                $request->validate(['jenis' => 'required|exists:transportasis,id']);
                $tr                      = Transportasi::findOrFail($request->jenis);
                $data['transportasi_id'] = $tr->id;
                $emisi                   = $tr->factor_emisi * $validated['nilai_input'] * $jumlah_orang;
                break;

            case 'biaya':
                $request->validate(['jenisKendaraan' => 'required|exists:biayas,id']);
                $bi               = Biaya::findOrFail($request->jenisKendaraan);
                $data['biaya_id'] = $bi->id;
                $emisi            = $bi->factorEmisi * $validated['nilai_input'] * $jumlah_orang;
                break;
        }

        $data['hasil_emisi'] = $emisi;

        HasilPerhitungan::create($data);

        return redirect()->route('admin.perhitungan.index')
            ->with('success', 'Perhitungan emisi berhasil disimpan: ' . round($emisi, 4) . ' kg CO₂');
    }
    public function show($id)
    {
        $this->checkAuthorization(auth()->user(), ['perhitungan.view']);

        $user = auth()->user();

        $perhitungan = HasilPerhitungan::with(['bahanBakar', 'transportasi', 'biaya', 'user.karyawan.perusahaan'])
            ->findOrFail($id);

        // Jika user karyawan, hanya boleh melihat milik sendiri
        if ($user->hasRole('karyawan') && $perhitungan->user_id !== $user->id) {
            return redirect()->route('admin.perhitungan.index')
                ->withErrors('Anda tidak diizinkan melihat data ini.');
        }

        // Jika user perusahaan, hanya boleh melihat milik karyawan perusahaannya
        if ($user->hasRole('perusahaan')) {
            $karyawanIds = \App\Models\Karyawan::where('perusahaan_id', $user->perusahaan->id)->pluck('user_id');
            if (! $karyawanIds->contains($perhitungan->user_id)) {
                return redirect()->route('admin.perhitungan.index')
                    ->withErrors('Anda tidak diizinkan melihat data ini.');
            }
        }

        // Admin dan superadmin bisa melihat semua, tanpa pembatasan

        return view('backend.pages.perhitungan.show', compact('perhitungan'));
    }

    public function edit($id)
    {
        $this->checkAuthorization(auth()->user(), ['perhitungan.edit']);

        $perhitungan = HasilPerhitungan::findOrFail($id);
        $user        = auth()->user();

        // Hanya izinkan jika user adalah pemilik data atau punya peran admin/superadmin
        if ($user->hasRole('Karyawan') && $perhitungan->user_id !== $user->id) {
            return redirect()->route('admin.perhitungan.index')
                ->withErrors('Anda tidak diizinkan mengedit data ini.');
        }

        $kategori = $perhitungan->kategori;
        $metode   = $perhitungan->metode;

        $bahanBakar     = BahanBakar::where('kategori', $kategori)->get();
        $jenis          = Transportasi::where('kategori', $kategori)->get();
        $jenisKendaraan = Biaya::where('kategori', $kategori)->get();

        return view('backend.pages.perhitungan.edit', compact(
            'perhitungan',
            'kategori',
            'metode',
            'bahanBakar',
            'jenis',
            'jenisKendaraan',
        ));
    }

    public function update(Request $request, $id)
    {
        $this->checkAuthorization(auth()->user(), ['perhitungan.edit']);

        $perhitungan = HasilPerhitungan::findOrFail($id);

        // Hanya izinkan jika user adalah pemilik data atau punya peran admin/superadmin
        if ($perhitungan->user_id !== auth()->id() && ! $user()->hasAnyRole(['Admin', 'Superadmin'])) {
            return redirect()->route('admin.perhitungan.index')
                ->withErrors('Anda tidak diizinkan mengubah data ini.');
        }

        $validated = $request->validate([
            'kategori'     => 'required|string',
            'tanggal'      => 'required|date',
            'jumlah_orang' => 'nullable|numeric|min:1',
            'nilai_input'  => 'required|numeric|min:0',
            'metode'       => 'required|in:bahan_bakar,jarak_tempuh,biaya',
            'titik_awal'   => 'required|string|max:255',
            'titik_tujuan' => 'required|string|max:255',
        ]);

        $jumlah_orang = $validated['jumlah_orang'] ?? 1;
        $emisi        = 0;

        $perhitungan->fill($validated);
        $perhitungan->jumlah_orang = $jumlah_orang;

        // Reset foreign key
        $perhitungan->bahan_bakar_id  = null;
        $perhitungan->transportasi_id = null;
        $perhitungan->biaya_id        = null;

        switch ($validated['metode']) {
            case 'bahan_bakar':
                $request->validate(['Bahan_bakar' => 'required|exists:bahan_bakars,id']);
                $bb                          = BahanBakar::findOrFail($request->Bahan_bakar);
                $perhitungan->bahan_bakar_id = $bb->id;
                $emisi                       = $bb->factorEmisi * $validated['nilai_input'] * $jumlah_orang;
                break;

            case 'jarak_tempuh':
                $request->validate(['jenis' => 'required|exists:transportasis,id']);
                $tr                           = Transportasi::findOrFail($request->jenis);
                $perhitungan->transportasi_id = $tr->id;
                $emisi                        = $tr->factor_emisi * $validated['nilai_input'] * $jumlah_orang;
                break;

            case 'biaya':
                $request->validate(['jenisKendaraan' => 'required|exists:biayas,id']);
                $bi                    = Biaya::findOrFail($request->jenisKendaraan);
                $perhitungan->biaya_id = $bi->id;
                $emisi                 = $bi->factorEmisi * $validated['nilai_input'] * $jumlah_orang;
                break;
        }

        $perhitungan->hasil_emisi = $emisi;
        $perhitungan->save();

        return redirect()->route('admin.perhitungan.index')
            ->with('success', 'Data berhasil diperbarui. Emisi: ' . round($emisi, 4) . ' kg CO₂');
    }

    public function destroy($id)
    {
        $this->checkAuthorization(auth()->user(), ['perhitungan.delete']);
        $data = HasilPerhitungan::findOrFail($id);

        if ($data->user_id !== auth()->id()) {
            return redirect()->route('admin.perhitungan.index')
                ->withErrors('Anda tidak memiliki izin untuk menghapus data ini.');
        }

        $data->delete();

        return redirect()->route('admin.perhitungan.index')
            ->with('success', 'Data perhitungan berhasil dihapus.');
    }
}
