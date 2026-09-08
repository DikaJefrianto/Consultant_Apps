<?php

namespace App\Policies;

use App\Models\HasilPerhitungan;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Perusahaan; // Pastikan model Perusahaan diimport

class HasilPerhitunganPolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        // Admin, Superadmin, Perusahaan, dan Karyawan diizinkan melihat daftar
        // Filtering data yang terlihat akan ditangani di controller berdasarkan peran
        return $user->hasAnyRole(['admin', 'superadmin', 'perusahaan', 'karyawan']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\HasilPerhitungan  $hasilPerhitungan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, HasilPerhitungan $hasilPerhitungan)
    {
        if ($user->hasRole('admin') || $user->hasRole('superadmin')) {
            return true;
        }

        if ($user->hasRole('perusahaan')) {
            // Cek jika perhitungan dilakukan oleh user perusahaan itu sendiri
            if ($hasilPerhitungan->user_id === $user->id) {
                return true;
            }

            // Cek jika perhitungan dilakukan oleh karyawan dari perusahaan ini
            // Asumsi: User Perusahaan memiliki relasi ke model Perusahaan ($user->perusahaan)
            // Atau memiliki kolom $user->perusahaan_id
            $userPerusahaanId = null;
            if ($user->perusahaan) {
                $userPerusahaanId = $user->perusahaan->id;
            } elseif (isset($user->perusahaan_id)) {
                $userPerusahaanId = $user->perusahaan_id;
            }

            if ($userPerusahaanId) {
                $karyawan = Karyawan::where('user_id', $hasilPerhitungan->user_id)->first();
                if ($karyawan && $karyawan->perusahaan_id === $userPerusahaanId) {
                    return true;
                }
            }
        }

        if ($user->hasRole('karyawan')) {
            return $user->id === $hasilPerhitungan->user_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->hasAnyRole(['admin', 'superadmin', 'perusahaan', 'karyawan']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, HasilPerhitungan $hasilPerhitungan)
    {
        if ($user->hasRole('admin') || $user->hasRole('superadmin')) {
            return true;
        }
        // Perusahaan dapat mengedit perhitungan milik mereka atau karyawan mereka
        if ($user->hasRole('perusahaan')) {
            // Cek jika perhitungan dilakukan oleh user perusahaan itu sendiri
            if ($hasilPerhitungan->user_id === $user->id) {
                return true;
            }
            $userPerusahaanId = null;
            if ($user->perusahaan) {
                $userPerusahaanId = $user->perusahaan->id;
            } elseif (isset($user->perusahaan_id)) {
                $userPerusahaanId = $user->perusahaan_id;
            }

            if ($userPerusahaanId) {
                $karyawan = Karyawan::where('user_id', $hasilPerhitungan->user_id)->first();
                if ($karyawan && $karyawan->perusahaan_id === $userPerusahaanId) {
                    return true;
                }
            }
        }
        // Karyawan hanya bisa mengedit perhitungan yang mereka sendiri lakukan
        if ($user->hasRole('karyawan')) {
            return $user->id === $hasilPerhitungan->user_id;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, HasilPerhitungan $hasilPerhitungan)
    {
        if ($user->hasRole('admin') || $user->hasRole('superadmin')) {
            return true;
        }
        // Perusahaan dapat menghapus perhitungan milik mereka atau karyawan mereka
        if ($user->hasRole('perusahaan')) {
            // Cek jika perhitungan dilakukan oleh user perusahaan itu sendiri
            if ($hasilPerhitungan->user_id === $user->id) {
                return true;
            }
            $userPerusahaanId = null;
            if ($user->perusahaan) {
                $userPerusahaanId = $user->perusahaan->id;
            } elseif (isset($user->perusahaan_id)) {
                $userPerusahaanId = $user->perusahaan_id;
            }

            if ($userPerusahaanId) {
                $karyawan = Karyawan::where('user_id', $hasilPerhitungan->user_id)->first();
                if ($karyawan && $karyawan->perusahaan_id === $userPerusahaanId) {
                    return true;
                }
            }
        }
        // Karyawan hanya bisa menghapus perhitungan yang mereka sendiri lakukan
        if ($user->hasRole('karyawan')) {
            return $user->id === $hasilPerhitungan->user_id;
        }
        return false;
    }

    // ... (metode restore dan forceDelete tetap sama jika ada) ...
}
