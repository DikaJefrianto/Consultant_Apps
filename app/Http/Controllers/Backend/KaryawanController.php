<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class KaryawanController extends Controller
{
    /**
     * Menampilkan daftar karyawan dengan fitur pencarian.
     */
    public function index(Request $request): Renderable
    {
        $user = auth()->user();
        $this->checkAuthorization($user, ['karyawan.view']);

        $search = $request->query('search');

        $query = Karyawan::with('perusahaan', 'user');

        // Jika role-nya perusahaan, batasi hanya pada karyawan miliknya
        if ($user->hasRole('Perusahaan') && $user->perusahaan) {
            $query->where('perusahaan_id', $user->perusahaan->id);
        }

        // Jika ada keyword pencarian, filter berdasarkan nama atau email user
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('email', 'like', '%' . $search . '%');
                    });
            });
        }

        $karyawans = $query->latest()->paginate(10)->withQueryString();

        return view('backend.pages.karyawans.index', compact('karyawans'));
    }


    /**
     * Menampilkan form untuk membuat karyawan baru.
     */
    public function create(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['karyawan.create']);

        $perusahaans = Perusahaan::all();
        return view('backend.pages.karyawans.create', compact('perusahaans'));
    }

    /**
     * Menyimpan data karyawan baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['karyawan.create']);

        $data = $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8|confirmed',
            'perusahaan_id' => 'required|exists:perusahaans,id',
            'no_hp'         => 'required|string|max:15',
            'alamat'        => 'required|string|max:255',
            'jabatan'       => 'required|string|max:255',
        ]);


        $user = User::create([
            'name'     => $data['nama_lengkap'],
            'email'    => $data['email'],
            'username' => strtolower(str_replace(' ', '_', $data['nama_lengkap'])),
            'password' => Hash::make($data['password']),
        ]);
        $user->assignRole('Karyawan');

        Karyawan::create([
            'nama_lengkap'  => $data['nama_lengkap'],
            'no_hp'         => $data['no_hp'],
            'alamat'        => $data['alamat'],
            'jabatan'       => $data['jabatan'],
            'user_id'       => $user->id,
            'perusahaan_id' => $data['perusahaan_id'],
        ]);

        return redirect()->route('admin.karyawans.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail karyawan tertentu.
     */
    public function show(int $id): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['karyawan.view']);

        // Menggunakan findOrFail secara eksplisit
        $karyawan = Karyawan::with('user', 'perusahaan')->findOrFail($id);

        return view('backend.pages.karyawans.show', compact('karyawan'));
    }

    /**
     * Menampilkan form untuk mengedit karyawan tertentu.
     */
    public function edit(int $id): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['karyawan.edit']);

        // Menggunakan findOrFail secara eksplisit
        $karyawan = Karyawan::with('user')->findOrFail($id);

        $perusahaans = Perusahaan::all();
        return view('backend.pages.karyawans.edit', compact('karyawan', 'perusahaans'));
    }

    /**
     * Memperbarui data karyawan tertentu di database.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['karyawan.edit']);

        // Menggunakan findOrFail secara eksplisit
        $karyawan = Karyawan::findOrFail($id);

        $data = $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'email'         => ['required', 'email', Rule::unique('users', 'email')->ignore($karyawan->user_id)],
            'password'      => 'nullable|string|min:8|confirmed',
            'perusahaan_id' => 'required|exists:perusahaans,id',
            'no_hp'         => 'nullable|string|max:15',
            'alamat'        => 'nullable|string|max:255',
            'jabatan'       => 'nullable|string|max:255',
        ]);

        $user = $karyawan->user;
        $user->name = $data['nama_lengkap'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        $karyawan->update([
            'nama_lengkap'  => $data['nama_lengkap'],
            'no_hp'         => $data['no_hp'],
            'alamat'        => $data['alamat'],
            'jabatan'       => $data['jabatan'],
            'perusahaan_id' => $data['perusahaan_id'],
        ]);

        return redirect()->route('admin.karyawans.index')
            ->with('success', 'Karyawan berhasil diperbarui.');
    }

    /**
     * Menghapus karyawan tertentu dari database.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['karyawan.delete']);

        // Menggunakan findOrFail secara eksplisit
        $karyawan = Karyawan::findOrFail($id);

        if ($karyawan->user) {
            $karyawan->user->delete();
        }

        $karyawan->delete();

        return redirect()->route('admin.karyawans.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }
}
