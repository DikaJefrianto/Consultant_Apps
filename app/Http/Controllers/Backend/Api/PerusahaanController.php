<?php

namespace App\Http\Controllers\Backend\Api;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use App\Models\User; // Import model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Import Hash Facade
use Illuminate\Validation\ValidationException; // Import ValidationException

class PerusahaanController extends Controller
{
    /**
     * Menampilkan daftar perusahaan.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        $perusahaans = Perusahaan::with('user')->get(); // Load relasi user jika ada
        return response()->json([
            'success' => true,
            'message' => 'Daftar perusahaan berhasil diambil',
            'data' => $perusahaans
        ], 200);
    }

    /**
     * Menyimpan data perusahaan baru beserta akun user-nya.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validasi data untuk pembuatan User dan Perusahaan
            $validatedData = $request->validate([
                'nama' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users,username', // Unique di tabel users
                'email' => 'required|email|max:255|unique:users,email', // Unique di tabel users
                'password' => 'required|string|min:8|confirmed', // 'confirmed' butuh password_confirmation
                'alamat' => 'nullable|string|max:255',
                'keterangan' => 'nullable|string',
            ]);

            // Buat User baru
            $user = User::create([
                'name' => $validatedData['nama'], // Gunakan nama perusahaan sebagai nama user
                'email' => $validatedData['email'],
                'username' => $validatedData['username'],
                'password' => Hash::make($validatedData['password']),
            ]);


            // $user->assignRole('Perusahaan'); // Misalnya, jika menggunakan Spatie Permission

            // Buat Perusahaan baru dan kaitkan dengan user
            $perusahaan = Perusahaan::create([
                'user_id' => $user->id, // Kaitkan dengan ID user yang baru dibuat
                'nama' => $validatedData['nama'],
                'username' => $validatedData['username'], // Simpan username di perusahaan juga jika perlu
                'email' => $validatedData['email'],       // Simpan email di perusahaan juga jika perlu
                'alamat' => $validatedData['alamat'] ?? null,
                'keterangan' => $validatedData['keterangan'] ?? null,
            ]);

            // Load user relation untuk response
            $perusahaan->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Perusahaan dan akun berhasil ditambahkan',
                'data' => $perusahaan,
            ], 201); // 201 Created
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422); // 422 Unprocessable Entity
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan perusahaan',
                'error' => $e->getMessage()
            ], 500); // 500 Internal Server Error
        }
    }

    /**
     * Menampilkan detail perusahaan berdasarkan ID.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $perusahaan = Perusahaan::with('user')->find($id);

        if (!$perusahaan) {
            return response()->json([
                'success' => false,
                'message' => 'Perusahaan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail perusahaan berhasil diambil',
            'data' => $perusahaan
        ], 200);
    }

    /**
     * Memperbarui data perusahaan dan akun user terkait.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $perusahaan = Perusahaan::with('user')->find($id);

            if (!$perusahaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perusahaan tidak ditemukan',
                ], 404);
            }

            // Dapatkan user terkait
            $user = $perusahaan->user;

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun user terkait perusahaan tidak ditemukan. Tidak dapat memperbarui.',
                ], 404);
            }

            // Validasi data. Perhatikan 'sometimes' untuk opsional.
            // Unique rule untuk username dan email harus mengecualikan ID user yang sedang diupdate.
            $validatedData = $request->validate([
                'nama' => 'sometimes|string|max:255',
                'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id,
                'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
                'password' => 'sometimes|string|min:8|confirmed', // Jika password diupdate
                'alamat' => 'nullable|string|max:255',
                'keterangan' => 'nullable|string',
            ]);

            // Update data user
            $userUpdateData = [];
            if (isset($validatedData['nama'])) {
                $userUpdateData['name'] = $validatedData['nama'];
            }
            if (isset($validatedData['email'])) {
                $userUpdateData['email'] = $validatedData['email'];
            }
            if (isset($validatedData['username'])) {
                $userUpdateData['username'] = $validatedData['username'];
            }
            if (isset($validatedData['password'])) {
                $userUpdateData['password'] = Hash::make($validatedData['password']);
            }
            if (!empty($userUpdateData)) {
                $user->update($userUpdateData);
            }

            // Update data perusahaan
            $perusahaanUpdateData = [];
            if (isset($validatedData['nama'])) {
                $perusahaanUpdateData['nama'] = $validatedData['nama'];
            }
            if (isset($validatedData['email'])) {
                $perusahaanUpdateData['email'] = $validatedData['email'];
            }
            if (isset($validatedData['username'])) {
                $perusahaanUpdateData['username'] = $validatedData['username'];
            }
            if (isset($validatedData['alamat'])) {
                $perusahaanUpdateData['alamat'] = $validatedData['alamat'];
            }
            if (isset($validatedData['keterangan'])) {
                $perusahaanUpdateData['keterangan'] = $validatedData['keterangan'];
            } else if ($request->has('keterangan') && $validatedData['keterangan'] === null) {
                // Handle case where keterangan is explicitly set to null
                $perusahaanUpdateData['keterangan'] = null;
            }

            if (!empty($perusahaanUpdateData)) {
                $perusahaan->update($perusahaanUpdateData);
            }

            // Reload user relation for response
            $perusahaan->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Perusahaan dan akun berhasil diperbarui',
                'data' => $perusahaan,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui perusahaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus data perusahaan dan akun user terkait.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $perusahaan = Perusahaan::find($id);

        if (!$perusahaan) {
            return response()->json([
                'success' => false,
                'message' => 'Perusahaan tidak ditemukan',
            ], 404);
        }

        // Hapus user terkait jika ada (penting: pastikan ada foreign key cascade delete di database,
        // atau hapus secara manual di sini jika user_id adalah foreign key)
        if ($perusahaan->user) {
            $perusahaan->user->delete();
        }

        $perusahaan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Perusahaan dan akun terkait berhasil dihapus',
        ], 200);
    }
}
