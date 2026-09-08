<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use App\Models\Strategi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // Perlu Auth untuk mendapatkan user yang sedang login
use Illuminate\Facades\Response;


class StrategiController extends Controller
{
    /**
     * Helper function to get the authenticated user's associated Perusahaan ID.
     * Returns null if the user is an 'admin' or 'super-admin' role,
     * or if they are a regular user not linked to a company.
     *
     * @return int|null The ID of the authenticated user's company, or null if not found/admin.
     */
    private function getAuthenticatedPerusahaanId(): ?int
    {
        if (Auth::check()) {
            // Jika user punya role 'admin' atau 'super-admin', mereka tidak terikat perusahaan spesifik
            if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Superadmin')) {
                return null;
            }

            // Untuk user biasa (non-admin/non-super-admin), cari perusahaan_id mereka
            // Asumsi model Perusahaan memiliki kolom 'user_id' yang merujuk ke ID user.
            $perusahaan = Perusahaan::where('user_id', Auth::id())->first();
            return $perusahaan ? $perusahaan->id : null;
        }
        return null;
    }

    /**
     * Helper function to check if the authenticated user has admin/super-admin role.
     *
     * @return bool
     */
    private function isAdminOrSuperAdmin(): bool
    {
        return Auth::check() && (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Superadmin'));
    }

    /**
     * Display a listing of the strategi.
     */
    public function index(): Renderable
    {
        // Pengecekan otorisasi umum (dari trait AuthorizationChecker Anda)
        $this->checkAuthorization(auth()->user(), ['strategi.view']);

        $perusahaanId = $this->getAuthenticatedPerusahaanId();
        $strategis = collect(); // Inisialisasi sebagai koleksi kosong

        if ($this->isAdminOrSuperAdmin()) {
            // Admin/Super Admin: Bisa melihat semua strategi
            $strategis = Strategi::with('perusahaan')->latest()->paginate(10);
        } elseif (!is_null($perusahaanId)) {
            // Pengguna biasa yang terhubung ke perusahaan: Hanya melihat strategi perusahaan mereka
            $strategis = Strategi::with('perusahaan')
                                ->where('perusahaan_id', $perusahaanId)
                                ->latest()
                                ->paginate(10);
        }
        // Jika bukan admin/super-admin dan tidak terhubung ke perusahaan, $strategis akan tetap kosong.


        return view('backend.pages.strategis.index', compact('strategis'));
    }

    /**
     * Show the form for creating a new strategi.
     *
     * @return Renderable|RedirectResponse // Deklarasi tipe return yang diperbarui
     */
    public function create(): Renderable|RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['strategi.create']);

        $perusahaanId = $this->getAuthenticatedPerusahaanId();
        $perusahaans = collect(); // Inisialisasi sebagai koleksi kosong

        if ($this->isAdminOrSuperAdmin()) {
            // Admin/Super Admin: Tampilkan semua perusahaan untuk dipilih di dropdown
            $perusahaans = Perusahaan::all();
        } elseif (is_null($perusahaanId)) {
            // Pengguna biasa yang TIDAK terhubung ke perusahaan: Redirect ke dashboard
            // Karena mereka tidak diizinkan membuat strategi tanpa afiliasi perusahaan
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses untuk membuat strategi tanpa terhubung dengan perusahaan.');
        } else {
            // Pengguna biasa yang terhubung ke perusahaan: Hanya tampilkan perusahaan mereka sendiri untuk dipilih
            $perusahaans = Perusahaan::where('id', $perusahaanId)->get();
        }

        return view('backend.pages.strategis.create', compact('perusahaans'));
    }

    /**
     * Store a newly created strategi in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $perusahaanId = $this->getAuthenticatedPerusahaanId();
        $isAdmin = $this->isAdminOrSuperAdmin();

        // Validasi input umum
        $validatedData = $request->validate([
            'nama_program' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
            'dokumen' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            // 'perusahaan_id' selalu diperlukan di request, namun validasinya bergantung pada role
            'perusahaan_id' => 'required|integer|exists:perusahaans,id',
        ]);

        $targetPerusahaanId = null;

        if ($isAdmin) {
            // Admin/Super Admin: 'perusahaan_id' diambil langsung dari input request (mereka bisa memilih)
            $targetPerusahaanId = (int) $validatedData['perusahaan_id'];
        } elseif (is_null($perusahaanId)) {
            // Pengguna non-admin yang TIDAK terhubung ke perusahaan: Tolak pembuatan strategi
            return redirect()->back()->with('error', 'Anda tidak diizinkan membuat strategi tanpa terhubung dengan perusahaan.');
        } else {
            // Pengguna biasa yang terhubung ke perusahaan: Pastikan 'perusahaan_id' yang di-request
            // cocok dengan 'perusahaan_id' mereka sendiri. Ini lapisan keamanan penting.
            if ((int) $validatedData['perusahaan_id'] !== $perusahaanId) {
                return redirect()->back()->with('error', 'Anda tidak diizinkan membuat strategi untuk perusahaan lain.');
            }
            $targetPerusahaanId = $perusahaanId; // Set 'perusahaan_id' dari user yang terautentikasi
        }

        // Penanganan upload dokumen
        if ($request->hasFile('dokumen')) {
            $validatedData['dokumen'] = $request->file('dokumen')->store('strategi_docs', 'public');
        } else {
            $validatedData['dokumen'] = null;
        }

        // Buat entri Strategi baru
        Strategi::create([
            'perusahaan_id' => $targetPerusahaanId, // Gunakan ID perusahaan yang sudah ditentukan
            'nama_program' => $validatedData['nama_program'],
            'deskripsi' => $validatedData['deskripsi'],
            'status' => $validatedData['status'],
            'dokumen' => $validatedData['dokumen'],
        ]);

        return redirect()->route('admin.strategis.index')->with('success', 'Strategy successfully added.');
    }

    /**
     * Display the specified strategi.
     */
    public function show(Strategi $strategi): Renderable
    {
        $perusahaanId = $this->getAuthenticatedPerusahaanId();

        if ($this->isAdminOrSuperAdmin()) {
            // Admin/Super Admin: Diizinkan melihat strategi apa pun.
            // Tidak ada pemeriksaan tambahan diperlukan di sini.
        } elseif (is_null($perusahaanId) || $strategi->perusahaan_id !== $perusahaanId) {
            // Pengguna biasa: Harus terhubung ke perusahaan DAN strategi harus milik perusahaan mereka.
            // Jika tidak, lemparkan error 403 (Unauthorized).
            abort(403, 'Unauthorized action.');
        }

        return view('backend.pages.strategis.show', compact('strategi'));
    }

    /**
     * Show the form for editing the specified strategi.
     */
    public function edit(Strategi $strategi): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['strategi.edit']);

        $perusahaanId = $this->getAuthenticatedPerusahaanId();
        $perusahaans = collect(); // Inisialisasi sebagai koleksi kosong

        if ($this->isAdminOrSuperAdmin()) {
            // Admin/Super Admin: Tampilkan semua perusahaan untuk dropdown.
            $perusahaans = Perusahaan::all();
        } elseif (is_null($perusahaanId) || $strategi->perusahaan_id !== $perusahaanId) {
            // Pengguna biasa: Harus terhubung ke perusahaan DAN strategi harus milik perusahaan mereka.
            abort(403, 'Unauthorized action.');
        } else {
            // Pengguna biasa yang terhubung ke perusahaan: Hanya tampilkan perusahaan mereka sendiri.
            $perusahaans = Perusahaan::where('id', $perusahaanId)->get();
        }

        return view('backend.pages.strategis.edit', compact('strategi', 'perusahaans'));
    }

    /**
     * Update the specified strategi in storage.
     */
    public function update(Request $request, Strategi $strategi): RedirectResponse
    {
        $perusahaanId = $this->getAuthenticatedPerusahaanId();
        $isAdmin = $this->isAdminOrSuperAdmin();

        if ($isAdmin) {
            // Admin/Super Admin: Diizinkan memperbarui strategi apa pun.
            // Pengecekan perusahaan_id akan dilakukan oleh $targetPerusahaanId di bawah.
        } elseif (is_null($perusahaanId) || $strategi->perusahaan_id !== $perusahaanId) {
            // Pengguna biasa: Harus terhubung ke perusahaan DAN strategi harus milik perusahaan mereka.
            abort(403, 'Unauthorized action.');
        }

        $validationRules = [
            'nama_program' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
            'dokumen' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'perusahaan_id' => 'required|integer|exists:perusahaans,id', // Selalu diperlukan
        ];

        $validatedData = $request->validate($validationRules);

        $targetPerusahaanId = null;
        if ($isAdmin) {
            // Admin/Super Admin: 'perusahaan_id' dapat diperbarui dari input request.
            $targetPerusahaanId = (int) $validatedData['perusahaan_id'];
        } else {
            // Pengguna biasa: Pastikan 'perusahaan_id' dari request cocok dengan milik mereka.
            if ((int) $validatedData['perusahaan_id'] !== $perusahaanId) {
                return redirect()->back()->with('error', 'Anda tidak diizinkan mengubah strategi untuk perusahaan lain.');
            }
            $targetPerusahaanId = $perusahaanId;
        }

        // Terapkan 'perusahaan_id' yang telah ditentukan ke objek strategi
        $strategi->perusahaan_id = $targetPerusahaanId;

        // Penanganan upload dokumen
        if ($request->hasFile('dokumen')) {
            if ($strategi->dokumen && Storage::disk('public')->exists($strategi->dokumen)) {
                Storage::disk('public')->delete($strategi->dokumen);
            }
            $validatedData['dokumen'] = $request->file('dokumen')->store('strategi_docs', 'public');
        } else if ($request->boolean('remove_dokumen')) {
            if ($strategi->dokumen && Storage::disk('public')->exists($strategi->dokumen)) {
                Storage::disk('public')->delete($strategi->dokumen);
            }
            $validatedData['dokumen'] = null;
        }

        $strategi->fill([
            'nama_program' => $validatedData['nama_program'],
            'deskripsi' => $validatedData['deskripsi'],
            'status' => $validatedData['status'],
            'dokumen' => $validatedData['dokumen'] ?? $strategi->dokumen, // Pertahankan dokumen lama jika tidak ada yang baru
        ])->save();

        return redirect()->route('admin.strategis.index')->with('success', 'Strategy successfully updated.');
    }

    /**
     * Remove the specified strategi from storage.
     */
    public function destroy(Strategi $strategi): RedirectResponse
    {
        $perusahaanId = $this->getAuthenticatedPerusahaanId();
        $isAdmin = $this->isAdminOrSuperAdmin();

        if ($isAdmin) {
            // Admin/Super Admin: Diizinkan menghapus strategi apa pun.
        } elseif (is_null($perusahaanId) || $strategi->perusahaan_id !== $perusahaanId) {
            // Pengguna biasa: Harus terhubung ke perusahaan DAN strategi harus milik perusahaan mereka.
            abort(403, 'Unauthorized action.');
        }

        // Hapus dokumen terkait jika ada
        if ($strategi->dokumen && Storage::disk('public')->exists($strategi->dokumen)) {
            Storage::disk('public')->delete($strategi->dokumen);
        }

        $strategi->delete();

        return redirect()->route('admin.strategis.index')->with('success', 'Strategy successfully deleted.');
    }
    public function downloadDokumen(Strategi $strategi)
    {
        // 1. Otorisasi: Pastikan user berhak mengunduh dokumen ini
        $perusahaanId = $this->getAuthenticatedPerusahaanId();

        if ($this->isAdminOrSuperAdmin()) {
            // Admin/Super Admin boleh mengunduh dokumen strategi apa pun
        } elseif (is_null($perusahaanId) || $strategi->perusahaan_id !== $perusahaanId) {
            // User perusahaan hanya boleh mengunduh dokumen milik perusahaan mereka
            return redirect()->back()->with('error', 'Anda tidak diizinkan mengakses dokumen ini.');
           
            // abort(403, 'Unauthorized action.');
        }

        // 2. Pastikan dokumen ada
        if (!$strategi->dokumen || !Storage::disk('public')->exists($strategi->dokumen)) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');

            // abort(404, 'Dokumen tidak ditemukan.');
        }

        // 3. Ambil path file dan nama file
        $path = Storage::disk('public')->path($strategi->dokumen);
        $filename = basename($strategi->dokumen); // Ambil nama file dari path

        // 4. Paksa pengunduhan file
        return Response::download($path, $filename);
    }
}
