<?php

namespace App\Http\Controllers\Backend\Api;

use App\Http\Controllers\Controller;
use App\Models\Karyawan; // Pastikan ini diimpor dengan benar
use App\Models\User;     // Import model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Import Hash Facade
use Illuminate\Validation\ValidationException; // Import ValidationException

class KaryawanController extends Controller
{
    /**
     * Menampilkan daftar karyawan.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        $karyawans = Karyawan::with('user', 'perusahaan')->get(); // Load relasi user dan perusahaan
        return response()->json([
            'success' => true,
            'message' => 'Daftar karyawan berhasil diambil',
            'data' => $karyawans
        ], 200);
    }

    /**
     * Menyimpan data karyawan baru beserta akun user-nya.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validasi data untuk pembuatan User dan Karyawan
            $validatedData = $request->validate([
                'perusahaan_id' => 'required|exists:perusahaans,id', // Karyawan harus terkait perusahaan
                'nama'          => 'required|string|max:255',
                'username'      => 'required|string|max:255|unique:users,username', // Unique di tabel users
                'email'         => 'required|email|max:255|unique:users,email', // Unique di tabel users
                'password'      => 'required|string|min:8|confirmed', // 'confirmed' butuh password_confirmation
                'no_telepon'    => 'nullable|string|max:20',
                'alamat'        => 'nullable|string|max:255',
                'jabatan'       => 'nullable|string|max:100',
            ]);

            // Buat User baru untuk karyawan ini
            $user = User::create([
                'name'     => $validatedData['nama'], // Gunakan nama karyawan sebagai nama user
                'email'    => $validatedData['email'],
                'username' => $validatedData['username'],
                'password' => Hash::make($validatedData['password']),
            ]);


            // $user->assignRole('Karyawan'); // Misalnya, jika menggunakan Spatie Permission

            // Buat Karyawan baru dan kaitkan dengan user dan perusahaan
            $karyawan = Karyawan::create([
                'user_id'       => $user->id, // Kaitkan dengan ID user yang baru dibuat
                'perusahaan_id' => $validatedData['perusahaan_id'],
                'nama'          => $validatedData['nama'],
                'username'      => $validatedData['username'], // Simpan username di karyawan juga jika perlu
                'email'         => $validatedData['email'],       // Simpan email di karyawan juga jika perlu
                'no_telepon'    => $validatedData['no_telepon'] ?? null,
                'alamat'        => $validatedData['alamat'] ?? null,
                'jabatan'       => $validatedData['jabatan'] ?? null,
            ]);

            // Load user dan perusahaan relation untuk response
            $karyawan->load('user', 'perusahaan');

            return response()->json([
                'success' => true,
                'message' => 'Karyawan dan akun berhasil ditambahkan',
                'data' => $karyawan,
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
                'message' => 'Terjadi kesalahan saat menyimpan karyawan',
                'error' => $e->getMessage()
            ], 500); // 500 Internal Server Error
        }
    }

    /**
     * Menampilkan detail karyawan berdasarkan ID.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $karyawan = Karyawan::with('user', 'perusahaan')->find($id);

        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail karyawan berhasil diambil',
            'data' => $karyawan
        ], 200);
    }

    /**
     * Memperbarui data karyawan dan akun user terkait.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $karyawan = Karyawan::with('user', 'perusahaan')->find($id);

            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karyawan tidak ditemukan',
                ], 404);
            }

            // Dapatkan user terkait
            $user = $karyawan->user;

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun user terkait karyawan tidak ditemukan. Tidak dapat memperbarui.',
                ], 404);
            }

            // Validasi data. Perhatikan 'sometimes' untuk opsional.
            // Unique rule untuk username dan email harus mengecualikan ID user yang sedang diupdate.
            $validatedData = $request->validate([
                'perusahaan_id' => 'sometimes|required|exists:perusahaans,id', // perusahaan_id bisa diupdate, tapi tetap required jika disertakan
                'nama'          => 'sometimes|string|max:255',
                'username'      => 'sometimes|string|max:255|unique:users,username,' . $user->id,
                'email'         => 'sometimes|email|max:255|unique:users,email,' . $user->id,
                'password'      => 'sometimes|string|min:8|confirmed', // Jika password diupdate
                'no_telepon'    => 'nullable|string|max:20',
                'alamat'        => 'nullable|string|max:255',
                'jabatan'       => 'nullable|string|max:100',
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

            // Update data karyawan
            $karyawanUpdateData = [];
            if (isset($validatedData['perusahaan_id'])) {
                $karyawanUpdateData['perusahaan_id'] = $validatedData['perusahaan_id'];
            }
            if (isset($validatedData['nama'])) {
                $karyawanUpdateData['nama'] = $validatedData['nama'];
            }
            if (isset($validatedData['email'])) {
                $karyawanUpdateData['email'] = $validatedData['email'];
            }
            if (isset($validatedData['username'])) {
                $karyawanUpdateData['username'] = $validatedData['username'];
            }
            if (isset($validatedData['no_telepon'])) {
                $karyawanUpdateData['no_telepon'] = $validatedData['no_telepon'];
            }
            if (isset($validatedData['alamat'])) {
                $karyawanUpdateData['alamat'] = $validatedData['alamat'];
            }
            if (isset($validatedData['jabatan'])) {
                $karyawanUpdateData['jabatan'] = $validatedData['jabatan'];
            } else if ($request->has('jabatan') && $validatedData['jabatan'] === null) {
                // Handle case where jabatan is explicitly set to null
                $karyawanUpdateData['jabatan'] = null;
            }

            if (!empty($karyawanUpdateData)) {
                $karyawan->update($karyawanUpdateData);
            }

            // Reload user dan perusahaan relation for response
            $karyawan->load('user', 'perusahaan');

            return response()->json([
                'success' => true,
                'message' => 'Karyawan dan akun berhasil diperbarui',
                'data' => $karyawan,
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
                'message' => 'Terjadi kesalahan saat memperbarui karyawan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus data karyawan dan akun user terkait.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $karyawan = Karyawan::find($id);

        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan',
            ], 404);
        }

        // Hapus user terkait jika ada (penting: pastikan ada foreign key cascade delete di database,
        // atau hapus secara manual di sini jika user_id adalah foreign key)
        if ($karyawan->user) {
            $karyawan->user->delete();
        }

        $karyawan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Karyawan dan akun terkait berhasil dihapus',
        ], 200);
    }
}
