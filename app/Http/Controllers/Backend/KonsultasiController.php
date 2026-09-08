<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Konsultasi;
use App\Models\Perusahaan; // Import model Perusahaan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Illuminate\View\View;

class KonsultasiController extends Controller
{
    /**
     * Helper function to check if the authenticated user has admin/super-admin role.
     */
    private function isAdminOrSuperAdmin(): bool
    {
        return Auth::check() && (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Superadmin'));
    }

    /**
     * Helper function to get the authenticated user's associated Perusahaan ID.
     */
    private function getAuthenticatedPerusahaanId(): ?int
    {
        if (Auth::check()) {
            if ($this->isAdminOrSuperAdmin()) {
                return null;
            }
            // Asumsi user dengan role 'perusahaan' memiliki relasi ke perusahaan
            // atau ada user_id di tabel perusahaans yang merujuk ke user ini
            $perusahaan = Perusahaan::where('user_id', Auth::id())->first();
            return $perusahaan ? $perusahaan->id : null;
        }
        return null;
    }

    /**
     * Display a listing of the resource (konsultasi requests).
     */
    public function index(): Renderable|RedirectResponse
    {
        if ($this->isAdminOrSuperAdmin()) {
            // Admin/Superadmin melihat semua riwayat konsultasi
            $konsultasis = Konsultasi::with('perusahaan')->latest()->paginate(10);
        } else {
            // Perusahaan/Karyawan hanya melihat riwayat konsultasi perusahaan mereka
            $perusahaanId = $this->getAuthenticatedPerusahaanId();
            if (is_null($perusahaanId)) {
                return redirect()->route('admin.dashboard')->with('error', 'Anda tidak terkait dengan perusahaan manapun untuk melihat konsultasi.');
            }
            $konsultasis = Konsultasi::where('perusahaan_id', $perusahaanId)->with('perusahaan')->latest()->paginate(10);
        }

        return view('backend.pages.konsultasis.index', compact('konsultasis'));
    }
    public function approve(Request $request, Konsultasi $konsultasi)
    {
        // Otorisasi: Hanya Admin atau Super Admin yang dapat menyetujui
        if (!Auth::user()->hasRole(['Admin', 'Superadmin'])) {
            abort(403, 'Anda tidak memiliki izin untuk menyetujui konsultasi ini.');
        }

        // Pastikan status saat ini adalah 'pending' sebelum disetujui
        if ($konsultasi->status !== 'pending') {
            return redirect()->back()->with('error', 'Konsultasi ini tidak dapat disetujui karena statusnya bukan pending.');
        }

        $konsultasi->status = 'diterima';
        $konsultasi->waktu_disetujui = now(); // Set waktu persetujuan
        // Jika ada kolom `lokasi_disetujui` dan Anda ingin mengaturnya secara otomatis
        // atau melalui input tersembunyi, Anda bisa menambahkannya di sini.
        // Contoh: $konsultasi->lokasi_disetujui = 'Online via Google Meet';
        $konsultasi->save();

        return redirect()->route('admin.konsultasis.show', $konsultasi->id)->with('success', 'Konsultasi berhasil disetujui!');
    }

    /**
     * Show the form for creating a new konsultasi request.
     */
    public function create(): Renderable|RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['konsultasi.create']);// Dapatkan objek user yang sedang login


        // Jika bukan admin, lanjutkan pemeriksaan perusahaanId
        // $perusahaanId = $this->getAuthenticatedPerusahaanId();
        // if (is_null($perusahaanId)) {
        //     return redirect()->route('admin.dashboard')->with('error', 'Anda tidak terkait dengan perusahaan manapun untuk mengajukan konsultasi.');
        // }

        return view('backend.pages.konsultasis.create');
    }

    /**
     * Store a newly created konsultasi request in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Hanya user dengan role perusahaan/karyawan yang bisa mengajukan
        if ($this->isAdminOrSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $perusahaanId = $this->getAuthenticatedPerusahaanId();
        if (is_null($perusahaanId)) {
            return redirect()->back()->with('error', 'Anda tidak terkait dengan perusahaan manapun.');
        }

        $validatedData = $request->validate([
            'topik' => 'required|string|max:255',
            'lokasi_diajukan' => 'nullable|string|max:255',
            'waktu_diajukan' => 'nullable|date',
        ]);

        Konsultasi::create([
            'perusahaan_id' => $perusahaanId,
            'topik' => $validatedData['topik'],
            'lokasi_diajukan' => $validatedData['lokasi_diajukan'],
            'waktu_diajukan' => $validatedData['waktu_diajukan'],
            'status' => 'pending', // Default status
        ]);

        return redirect()->route('admin.konsultasis.index')->with('success', 'Pengajuan konsultasi berhasil dikirim. Menunggu persetujuan admin.');
    }

    /**
     * Display the specified konsultasi request.
     */
    public function show(Konsultasi $konsultasi): Renderable
    {
        // Admin/Superadmin: Bisa melihat semua detail
        // Perusahaan/Karyawan: Hanya bisa melihat detail milik perusahaannya
        if (!$this->isAdminOrSuperAdmin()) {
            $perusahaanId = $this->getAuthenticatedPerusahaanId();
            if (is_null($perusahaanId) || $konsultasi->perusahaan_id !== $perusahaanId) {
                abort(403, 'Unauthorized action.');
            }
        }
        return view('backend.pages.konsultasis.show', compact('konsultasi'));
    }

    /**
     * Show the form for editing the specified konsultasi request (Admin only).
     */
    public function edit(Konsultasi $konsultasi): Renderable
    {
        // Hanya Admin/Superadmin yang bisa mengedit/menyetujui
        if (!$this->isAdminOrSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        return view('backend.pages.konsultasis.edit', compact('konsultasi'));
    }

    /**
     * Update the specified konsultasi request in storage (Admin only).
     */
    public function update(Request $request, Konsultasi $konsultasi): RedirectResponse
    {
        // Hanya Admin/Superadmin yang bisa mengupdate/menyetujui
        if (!$this->isAdminOrSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'lokasi_disetujui' => 'nullable|string|max:255',
            'waktu_disetujui' => 'nullable|date',
            'catatan_admin' => 'nullable|string',
            'status' => 'required|string|in:pending,ditolak,diterima',
        ]);

        $konsultasi->update($validatedData);

        return redirect()->route('admin.konsultasis.index')->with('success', 'Status konsultasi berhasil diperbarui.');
    }

    /**
     * Remove the specified konsultasi request from storage (Admin only).
     */
    public function destroy(Konsultasi $konsultasi): RedirectResponse
    {
        // Hanya Admin/Superadmin yang bisa menghapus
        if (!$this->isAdminOrSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $konsultasi->delete();
        return redirect()->route('admin.konsultasis.index')->with('success', 'Pengajuan konsultasi berhasil dihapus.');
    }
}
