<?php

namespace App\Exports;

use App\Models\HasilPerhitungan;
use App\Models\Perusahaan; // Pastikan model ini ada
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LaporanDetailPerusahaanExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $perusahaanId;
    protected $bulan;
    protected $tahun;

    public function __construct(int $perusahaanId, int $bulan, int $tahun)
    {
        $this->perusahaanId = $perusahaanId;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Temukan perusahaan berdasarkan ID
        $perusahaan = Perusahaan::findOrFail($this->perusahaanId);

        // Dapatkan semua user_id yang terkait dengan perusahaan ini
        // Asumsi: Model Perusahaan memiliki relasi hasMany ke Karyawan, dan Karyawan memiliki user_id
        // Asumsi: User yang memiliki peran 'Perusahaan' juga bisa memiliki perhitungan
        $karyawan_user_ids = $perusahaan->karyawans->pluck('user_id')->toArray();
        $all_related_user_ids = array_unique(array_merge([$perusahaan->user_id], $karyawan_user_ids));

        // Ambil data perhitungan emisi detail
        $detailed_emission_data = HasilPerhitungan::with(['user', 'transportasi', 'bahanBakar', 'biaya'])
            ->whereIn('user_id', $all_related_user_ids)
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->orderBy('tanggal', 'asc')
            ->get();

        $exportData = collect();
        $no = 1;

        foreach ($detailed_emission_data as $perhitungan) {
            $jenis = 'N/A';
            if ($perhitungan->metode == 'bahan_bakar' && $perhitungan->bahanBakar) {
                $jenis = $perhitungan->bahanBakar->Bahan_bakar . ' (' . $perhitungan->bahanBakar->kategori . ')';
            } elseif ($perhitungan->metode == 'jarak_tempuh' && $perhitungan->transportasi) {
                $jenis = $perhitungan->transportasi->jenis . ' (' . $perhitungan->transportasi->kategori . ')';
            } elseif ($perhitungan->metode == 'biaya' && $perhitungan->biaya) {
                $jenis = $perhitungan->biaya->jenisKendaraan . ' (' . $perhitungan->biaya->kategori . ')';
            }

            $nilaiInputFormatted = number_format($perhitungan->nilai_input, 2, '.', '');
            if ($perhitungan->metode == 'bahan_bakar') $nilaiInputFormatted .= ' Liter';
            elseif ($perhitungan->metode == 'jarak_tempuh') $nilaiInputFormatted .= ' km';
            elseif ($perhitungan->metode == 'biaya') $nilaiInputFormatted = 'Rp ' . number_format($perhitungan->nilai_input, 0, '.', '');

            // === Bagian Sanitasi String untuk Mengatasi Masalah Encoding ===
            // Gunakan mb_convert_encoding untuk memastikan string adalah UTF-8 yang valid.
            // Jika ada karakter yang tidak valid, ini akan mencoba memperbaikinya.
            // Jika masalah tetap ada, Anda mungkin perlu mengidentifikasi kolom data yang spesifik.
            $karyawanName = mb_convert_encoding($perhitungan->user->name ?? 'N/A', 'UTF-8', 'UTF-8');
            $metode = mb_convert_encoding($perhitungan->metode, 'UTF-8', 'UTF-8');
            $jenisSanitized = mb_convert_encoding($jenis, 'UTF-8', 'UTF-8');
            $rute = mb_convert_encoding($perhitungan->titik_awal . ' - ' . $perhitungan->titik_tujuan, 'UTF-8', 'UTF-8');
            // === Akhir Bagian Sanitasi ===

            $exportData->push([
                $no++,
                $perhitungan->tanggal->format('Y-m-d H:i'),
                $karyawanName,
                $metode,
                $jenisSanitized,
                $nilaiInputFormatted,
                $perhitungan->jumlah_orang,
                number_format($perhitungan->hasil_emisi, 2, '.', ''),
                number_format($perhitungan->biaya->factorEmisi ?? 0, 0, '.', ''),
                $rute,
            ]);
        }

        return $exportData;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Karyawan',
            'Metode',
            'Jenis (BB/Transportasi/Biaya)',
            'Nilai Input',
            'Jumlah Orang',
            'Emisi (kg CO2)',
            'Biaya Terkait (Rp)',
            'Rute',
        ];
    }
}
