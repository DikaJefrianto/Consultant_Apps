<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use App\Models\HasilPerhitungan;
use App\Models\BahanBakar;
use App\Models\transportasi;
use App\Models\biaya;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanDetailPerusahaanExport; // Ini untuk detail, bukan ringkasan
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

// Import FromArray untuk export sederhana
use Maatwebsite\Excel\Concerns\FromArray;
// Import WithHeadings untuk menambahkan header ke Excel
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Jika Anda menggunakan Spatie, ini adalah cara yang lebih baik:
            // $this->middleware('permission:laporan.view');
            // Jika Anda memiliki metode checkAuthorization kustom, pastikan itu ada.
            // Contoh: $this->checkAuthorization(auth()->user(), ['laporan.view']);
            return $next($request);
        });
    }

    /**
     * Metode otorisasi dasar.
     * Ini harus kompatibel dengan metode di kelas induk (App\Http\Controllers\Controller).
     *
     * @param \App\Models\User $user
     * @param mixed $permissions (bisa string, array, atau null)
     * @param bool $ownPermissionCheck
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request): View
    {
        $this->checkAuthorization(auth()->user(), ['report.view']); // Panggilan tetap sama

        $bulan = (int) $request->input('bulan', date('m'));
        $tahun = (int) $request->input('tahun', date('Y'));

        // Dapatkan user yang sedang login
        $user = Auth::user();
        $perusahaanIdFilter = null;
        $relatedUserIds = [];

        // Jika user adalah 'perusahaan', filter hanya untuk perusahaan mereka
        if ($user && $user->hasRole('Perusahaan')) {
            $userPerusahaan = $user->perusahaan; // Asumsi user memiliki relasi hasOne ke Perusahaan
            if ($userPerusahaan) {
                $perusahaanIdFilter = $userPerusahaan->id;
                $relatedUserIds = $userPerusahaan->getAllRelatedUserIds();
            } else {
                // Jika user 'perusahaan' tidak terhubung ke perusahaan manapun, tampilkan kosong
                $perusahaans = collect();
                $availableYears = [date('Y')];
                $emisiPerBahanBakar = [];
                $emisiPerTransportasi = [];
                return view('backend.pages.laporan.index', compact(
                    'perusahaans',
                    'bulan',
                    'tahun',
                    'availableYears',
                    'emisiPerBahanBakar',
                    'emisiPerTransportasi'
                ));
            }
        }

        // Query untuk tahun yang tersedia
        $availableYearsQuery = HasilPerhitungan::select(DB::raw('YEAR(tanggal) as year'))
            ->distinct()
            ->orderBy('year', 'desc');

        // Terapkan filter berdasarkan user_id jika user adalah 'perusahaan'
        if (!empty($relatedUserIds)) {
            $availableYearsQuery->whereIn('user_id', $relatedUserIds);
        }
        $availableYears = $availableYearsQuery->pluck('year')->toArray();

        if (empty($availableYears)) {
            $availableYears = [date('Y')];
        }

        $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;

        $aggregatedDataQuery = HasilPerhitungan::select(
                    'karyawans.perusahaan_id',
                    DB::raw('SUM(hasil_perhitungans.hasil_emisi) as total_emisi'),
                    DB::raw('SUM(biayas.factorEmisi) as total_biaya_terkait'),
                    DB::raw('COUNT(hasil_perhitungans.id) as total_perjalanan')
                )
                ->join('users', 'hasil_perhitungans.user_id', '=', 'users.id')
                ->join('karyawans', 'users.id', '=', 'karyawans.user_id')
                ->leftJoin('biayas', 'hasil_perhitungans.biaya_id', '=', 'biayas.id')
                ->whereMonth('hasil_perhitungans.tanggal', $bulan)
                ->whereYear('hasil_perhitungans.tanggal', $tahun);

        // Terapkan filter berdasarkan user_id jika user adalah 'perusahaan'
        if (!empty($relatedUserIds)) {
            $aggregatedDataQuery->whereIn('hasil_perhitungans.user_id', $relatedUserIds);
        }

        $aggregatedData = $aggregatedDataQuery->groupBy('karyawans.perusahaan_id')
            ->get()
            ->keyBy('perusahaan_id');

        // Jika user adalah 'perusahaan', hanya ambil perusahaan mereka
        $perusahaans = Perusahaan::when($perusahaanIdFilter, function ($query) use ($perusahaanIdFilter) {
                $query->where('id', $perusahaanIdFilter);
            })
            ->get()
            ->map(function($perusahaan) use ($aggregatedData, $daysInMonth) {
                $perusahaan->total_emisi_filtered = 0;
                $perusahaan->total_biaya_filtered = 0;
                $perusahaan->total_perjalanan = 0;
                $perusahaan->rata_rata_harian = 0;

                if ($aggregatedData->has($perusahaan->id)) {
                    $data = $aggregatedData->get($perusahaan->id);
                    $perusahaan->total_emisi_filtered = $data->total_emisi;
                    $perusahaan->total_biaya_filtered = $data->total_biaya_terkait;
                    $perusahaan->total_perjalanan = $data->total_perjalanan;
                    if ($daysInMonth > 0) {
                        $perusahaan->rata_rata_harian = $data->total_emisi / $daysInMonth;
                    }
                }
                // Sanitasi nama perusahaan sebelum dikirim ke view
                $perusahaan->nama = mb_convert_encoding($perusahaan->nama, 'UTF-8', 'UTF-8');
                return $perusahaan;
            });

        $emisiPerBahanBakar = [];
        $emisiPerTransportasi = [];

        return view('backend.pages.laporan.index', compact(
            'perusahaans',
            'bulan',
            'tahun',
            'availableYears',
            'emisiPerBahanBakar',
            'emisiPerTransportasi'
        ));
    }

    /**
     * Mengekspor laporan ringkasan dalam format PDF.
     * Akan difilter berdasarkan perusahaan jika user adalah 'perusahaan'.
     */
    public function exportPdf(Request $request)
    {
        $this->checkAuthorization(auth()->user(), ['report.export']); // Panggilan tetap sama

        $lang = $request->input('lang', App::getLocale());

        $originalLocale = App::getLocale(); // Simpan locale asli
        App::setLocale($lang); // Atur locale untuk PDF

        $user = Auth::user();
        $perusahaanIdFilter = null;
        $relatedUserIds = [];

        if ($user && $user->hasRole('Perusahaan')) {
            $userPerusahaan = $user->perusahaan;
            if ($userPerusahaan) {
                $perusahaanIdFilter = $userPerusahaan->id;
                $relatedUserIds = $userPerusahaan->getAllRelatedUserIds();
            } else {
                // Jika user 'perusahaan' tidak terhubung ke perusahaan manapun, kembalikan PDF kosong atau error
                App::setLocale($originalLocale);
                abort(403, 'Anda tidak terhubung dengan perusahaan manapun.');
            }
        }

        try {
            $bulan = (int) $request->input('bulan', date('m'));
            $tahun = (int) $request->input('tahun', date('Y'));
            $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;

            // Menggunakan logika agregasi yang sama dengan index() untuk konsistensi
            $aggregatedDataQuery = HasilPerhitungan::select(
                            'karyawans.perusahaan_id',
                            DB::raw('SUM(hasil_perhitungans.hasil_emisi) as total_emisi'),
                            DB::raw('SUM(biayas.factorEmisi) as total_biaya_terkait'),
                            DB::raw('COUNT(hasil_perhitungans.id) as total_perjalanan')
                        )
                        ->join('users', 'hasil_perhitungans.user_id', '=', 'users.id')
                        ->join('karyawans', 'users.id', '=', 'karyawans.user_id')
                        ->leftJoin('biayas', 'hasil_perhitungans.biaya_id', '=', 'biayas.id')
                        ->whereMonth('hasil_perhitungans.tanggal', $bulan)
                        ->whereYear('hasil_perhitungans.tanggal', $tahun);

            if (!empty($relatedUserIds)) {
                $aggregatedDataQuery->whereIn('hasil_perhitungans.user_id', $relatedUserIds);
            }

            $aggregatedData = $aggregatedDataQuery->groupBy('karyawans.perusahaan_id')
                ->get()
                ->keyBy('perusahaan_id');

            $perusahaans = Perusahaan::when($perusahaanIdFilter, function ($query) use ($perusahaanIdFilter) {
                    $query->where('id', $perusahaanIdFilter);
                })
                ->get()
                ->map(function($perusahaan) use ($aggregatedData, $daysInMonth) {
                    $perusahaan->total_emisi_filtered = 0;
                    $perusahaan->total_biaya_filtered = 0;
                    $perusahaan->total_perjalanan = 0;
                    $perusahaan->rata_rata_harian = 0;

                    if ($aggregatedData->has($perusahaan->id)) {
                        $data = $aggregatedData->get($perusahaan->id);
                        $perusahaan->total_emisi_filtered = $data->total_emisi;
                        $perusahaan->total_biaya_filtered = $data->total_biaya_terkait;
                        $perusahaan->total_perjalanan = $data->total_perjalanan;
                        if ($daysInMonth > 0) {
                            $perusahaan->rata_rata_harian = $data->total_emisi / $daysInMonth;
                        }
                    }
                    return $perusahaan;
                });

            $pdf = Pdf::loadView('backend.pages.laporan.pdf_laporan', compact('perusahaans', 'bulan', 'tahun'));
            $periodString = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F_Y');
            $filename = 'laporan_emisi_perusahaan_' . $periodString . '.pdf';
            return $pdf->download($filename);
        } finally {
            App::setLocale($originalLocale);
        }
    }

    /**
     * Mengekspor laporan ringkasan dalam format CSV.
     * Akan difilter berdasarkan perusahaan jika user adalah 'perusahaan'.
     */
    public function exportCsv(Request $request)
    {
        $this->checkAuthorization(auth()->user(), ['report.export']); // Panggilan tetap sama

        $lang = $request->input('lang', App::getLocale());

        $originalLocale = App::getLocale(); // Simpan locale asli
        App::setLocale($lang); // Atur locale untuk CSV

        $user = Auth::user();
        $perusahaanIdFilter = null;
        $relatedUserIds = [];

        if ($user && $user->hasRole('Perusahaan')) {
            $userPerusahaan = $user->perusahaan;
            if ($userPerusahaan) {
                $perusahaanIdFilter = $userPerusahaan->id;
                $relatedUserIds = $userPerusahaan->getAllRelatedUserIds();
            } else {
                App::setLocale($originalLocale);
                abort(403, 'Anda tidak terhubung dengan perusahaan manapun.');
            }
        }

        try {
            $bulan = (int) $request->input('bulan', date('m'));
            $tahun = (int) $request->input('tahun', date('Y'));
            $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;

            // Menggunakan logika agregasi yang sama dengan index() untuk konsistensi
            $aggregatedDataQuery = HasilPerhitungan::select(
                            'karyawans.perusahaan_id',
                            DB::raw('SUM(hasil_perhitungans.hasil_emisi) as total_emisi'),
                            DB::raw('SUM(biayas.factorEmisi) as total_biaya_terkait'),
                            DB::raw('COUNT(hasil_perhitungans.id) as total_perjalanan')
                        )
                        ->join('users', 'hasil_perhitungans.user_id', '=', 'users.id')
                        ->join('karyawans', 'users.id', '=', 'karyawans.user_id')
                        ->leftJoin('biayas', 'hasil_perhitungans.biaya_id', '=', 'biayas.id')
                        ->whereMonth('hasil_perhitungans.tanggal', $bulan)
                        ->whereYear('hasil_perhitungans.tanggal', $tahun);

            if (!empty($relatedUserIds)) {
                $aggregatedDataQuery->whereIn('hasil_perhitungans.user_id', $relatedUserIds);
            }

            $aggregatedData = $aggregatedDataQuery->groupBy('karyawans.perusahaan_id')
                ->get()
                ->keyBy('perusahaan_id');

            $perusahaans = Perusahaan::when($perusahaanIdFilter, function ($query) use ($perusahaanIdFilter) {
                    $query->where('id', $perusahaanIdFilter);
                })
                ->get()
                ->map(function($perusahaan) use ($aggregatedData, $daysInMonth) {
                    $perusahaan->total_emisi_filtered = 0;
                    $perusahaan->total_biaya_filtered = 0;
                    $perusahaan->total_perjalanan = 0;
                    $perusahaan->rata_rata_harian = 0;

                    if ($aggregatedData->has($perusahaan->id)) {
                        $data = $aggregatedData->get($perusahaan->id);
                        $perusahaan->total_emisi_filtered = $data->total_emisi;
                        $perusahaan->total_biaya_filtered = $data->total_biaya_terkait;
                        $perusahaan->total_perjalanan = $data->total_perjalanan;
                        if ($daysInMonth > 0) {
                            $perusahaan->rata_rata_harian = $data->total_emisi / $daysInMonth;
                        }
                    }
                    return $perusahaan;
                });

            $periodString = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F_Y');
            $filename = 'laporan_emisi_perusahaan_' . $periodString . '.csv';
            $handle = fopen('php://temp', 'w+');

            fputcsv($handle, [
                'No', 'Nama Perusahaan', 'Total Emisi (kg CO2)', 'Rata-rata Harian (kg CO2)', 'Banyak Perjalanan', 'Periode'
            ]);

            $no = 1;
            foreach ($perusahaans as $perusahaan) {
                fputcsv($handle, [
                    $no++,
                    mb_convert_encoding($perusahaan->nama, 'UTF-8', 'UTF-8'),
                    number_format($perusahaan->total_emisi_filtered, 2, '.', ''),
                    number_format($perusahaan->rata_rata_harian, 2, '.', ''),
                    $perusahaan->total_perjalanan,
                    Carbon::createFromDate($tahun, $bulan)->translatedFormat('F Y')
                ]);
            }

            rewind($handle);
            $contents = stream_get_contents($handle);
            fclose($handle);

            return response($contents)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } finally {
            App::setLocale($originalLocale);
        }
    }

    /**
     * Menampilkan detail laporan untuk perusahaan tertentu.
     * Metode ini bernama 'showDetail' agar sesuai dengan Route::resources.
     */
    public function showDetail(Request $request, Perusahaan $perusahaan): View
    {
        // Otorisasi: Pastikan user 'perusahaan' hanya bisa melihat detail perusahaan mereka sendiri
        $user = Auth::user();
        if ($user && $user->hasRole('Perusahaan')) {
            $userPerusahaan = $user->perusahaan;
            if (!$userPerusahaan || $userPerusahaan->id !== $perusahaan->id) {
                abort(403, 'Anda tidak diizinkan melihat laporan perusahaan ini.');
            }
        }
        $this->checkAuthorization(auth()->user(), ['report.detail']); // Panggilan tetap sama

        $lang = $request->input('lang', App::getLocale());

        $originalLocale = App::getLocale(); // Simpan locale asli
        App::setLocale($lang); // Atur locale untuk PDF

        $bulan = (int) $request->input('bulan', date('m'));
        $tahun = (int) $request->input('tahun', date('Y'));

        // Ambil semua perhitungan emisi yang terkait dengan karyawan dari perusahaan ini
        // untuk periode yang dipilih.
        $detailPerhitungans = HasilPerhitungan::with(['user', 'transportasi', 'bahanBakar', 'biaya'])
            ->join('users', 'hasil_perhitungans.user_id', '=', 'users.id')
            ->join('karyawans', 'users.id', '=', 'karyawans.user_id')
            ->where('karyawans.perusahaan_id', $perusahaan->id)
            ->whereMonth('hasil_perhitungans.tanggal', $bulan)
            ->whereYear('hasil_perhitungans.tanggal', $tahun)
            ->orderBy('hasil_perhitungans.tanggal', 'asc')
            ->get();

        // Hitung ringkasan data untuk perusahaan ini (sama seperti di metode index/export)
        $totalEmisi = $detailPerhitungans->sum('hasil_emisi');
        $totalBiaya = $detailPerhitungans->sum(function($perhitungan) {
            return $perhitungan->biaya ? $perhitungan->biaya->factorEmisi : 0;
        });
        $totalPerjalanan = $detailPerhitungans->count();
        $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
        $rataRataHarian = ($daysInMonth > 0) ? $totalEmisi / $daysInMonth : 0;

        // === Sanitasi Data String untuk View Detail Laporan ===
        $perusahaan->nama = mb_convert_encoding($perusahaan->nama, 'UTF-8', 'UTF-8');

        $detailPerhitungans->map(function($perhitungan) {
            $perhitungan->user->name = mb_convert_encoding($perhitungan->user->name ?? 'N/A', 'UTF-8', 'UTF-8');
            $perhitungan->metode = mb_convert_encoding($perhitungan->metode, 'UTF-8', 'UTF-8');
            if ($perhitungan->bahanBakar) {
                $perhitungan->bahanBakar->Bahan_bakar = mb_convert_encoding($perhitungan->bahanBakar->Bahan_bakar, 'UTF-8', 'UTF-8');
                $perhitungan->bahanBakar->kategori = mb_convert_encoding($perhitungan->bahanBakar->kategori, 'UTF-8', 'UTF-8');
            }
            if ($perhitungan->transportasi) {
                $perhitungan->transportasi->jenis = mb_convert_encoding($perhitungan->transportasi->jenis, 'UTF-8', 'UTF-8');
                $perhitungan->transportasi->kategori = mb_convert_encoding($perhitungan->transportasi->kategori, 'UTF-8', 'UTF-8');
            }
            if ($perhitungan->biaya) {
                $perhitungan->biaya->jenisKendaraan = mb_convert_encoding($perhitungan->biaya->jenisKendaraan, 'UTF-8', 'UTF-8');
                $perhitungan->biaya->kategori = mb_convert_encoding($perhitungan->biaya->kategori, 'UTF-8', 'UTF-8');
            }
            $perhitungan->titik_awal = mb_convert_encoding($perhitungan->titik_awal, 'UTF-8', 'UTF-8');
            $perhitungan->titik_tujuan = mb_convert_encoding($perhitungan->titik_tujuan, 'UTF-8', 'UTF-8');
            return $perhitungan;
        });
        // === Akhir Sanitasi ===

        return view('backend.pages.laporan.detail_laporan', compact(
            'perusahaan',
            'bulan',
            'tahun',
            'detailPerhitungans',
            'totalEmisi',
            'totalBiaya',
            'totalPerjalanan',
            'rataRataHarian'
        ));
    }

    /**
     * Mengekspor detail laporan perusahaan dalam format PDF.
     */
    public function exportDetailPdf(Request $request, Perusahaan $perusahaan)
    {
        $lang = $request->input('lang', App::getLocale());

        $originalLocale = App::getLocale(); // Simpan locale asli
        App::setLocale($lang); // Atur locale untuk PDF

        // Otorisasi: Pastikan user 'perusahaan' hanya bisa mengekspor PDF perusahaan mereka sendiri
        $user = Auth::user();
        if ($user && $user->hasRole('Perusahaan')) {
            $userPerusahaan = $user->perusahaan;
            if (!$userPerusahaan || $userPerusahaan->id !== $perusahaan->id) {
                App::setLocale($originalLocale);
                abort(403, 'Anda tidak diizinkan mengekspor laporan perusahaan ini.');
            }
        }
        $this->checkAuthorization(auth()->user(), ['report.export_detail']); // Panggilan tetap sama

        try {
            $bulan = (int) $request->input('bulan', date('m'));
            $tahun = (int) $request->input('tahun', date('Y'));

            $detailPerhitungans = HasilPerhitungan::with(['user', 'transportasi', 'bahanBakar', 'biaya'])
                ->join('users', 'hasil_perhitungans.user_id', '=', 'users.id')
                ->join('karyawans', 'users.id', '=', 'karyawans.user_id')
                ->where('karyawans.perusahaan_id', $perusahaan->id)
                ->whereMonth('hasil_perhitungans.tanggal', $bulan)
                ->whereYear('hasil_perhitungans.tanggal', $tahun)
                ->orderBy('hasil_perhitungans.tanggal', 'asc')
                ->get();

            $totalEmisi = $detailPerhitungans->sum('hasil_emisi');
            $totalBiaya = $detailPerhitungans->sum(function($perhitungan) {
                return $perhitungan->biaya ? $perhitungan->biaya->factorEmisi : 0;
            });
            $totalPerjalanan = $detailPerhitungans->count();
            $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
            $rataRataHarian = ($daysInMonth > 0) ? $totalEmisi / $daysInMonth : 0;

            // === Sanitasi Data String untuk PDF Export Detail ===
            $perusahaan->nama = mb_convert_encoding($perusahaan->nama, 'UTF-8', 'UTF-8');
            $detailPerhitungans->map(function($perhitungan) {
                $perhitungan->user->name = mb_convert_encoding($perhitungan->user->name ?? 'N/A', 'UTF-8', 'UTF-8');
                $perhitungan->metode = mb_convert_encoding($perhitungan->metode, 'UTF-8', 'UTF-8');
                if ($perhitungan->bahanBakar) {
                    $perhitungan->bahanBakar->Bahan_bakar = mb_convert_encoding($perhitungan->bahanBakar->Bahan_bakar, 'UTF-8', 'UTF-8');
                    $perhitungan->bahanBakar->kategori = mb_convert_encoding($perhitungan->bahanBakar->kategori, 'UTF-8', 'UTF-8');
                }
                if ($perhitungan->transportasi) {
                    $perhitungan->transportasi->jenis = mb_convert_encoding($perhitungan->transportasi->jenis, 'UTF-8', 'UTF-8');
                    $perhitungan->transportasi->kategori = mb_convert_encoding($perhitungan->transportasi->kategori, 'UTF-8', 'UTF-8');
                }
                if ($perhitungan->biaya) {
                    $perhitungan->biaya->jenisKendaraan = mb_convert_encoding($perhitungan->biaya->jenisKendaraan, 'UTF-8', 'UTF-8');
                    $perhitungan->biaya->kategori = mb_convert_encoding($perhitungan->biaya->kategori, 'UTF-8', 'UTF-8');
                }
                $perhitungan->titik_awal = mb_convert_encoding($perhitungan->titik_awal, 'UTF-8', 'UTF-8');
                $perhitungan->titik_tujuan = mb_convert_encoding($perhitungan->titik_tujuan, 'UTF-8', 'UTF-8');
                return $perhitungan;
            });
            // === Akhir Sanitasi ===

            $pdf = Pdf::loadView('backend.pages.laporan.pdf_detail_laporan', compact(
                'perusahaan',
                'bulan',
                'tahun',
                'detailPerhitungans',
                'totalEmisi',
                'totalBiaya',
                'totalPerjalanan',
                'rataRataHarian'
            ));

            $periodString = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F_Y');
            $filename = 'detail_laporan_emisi_' . mb_convert_encoding($perusahaan->nama, 'UTF-8', 'UTF-8') . '_' . $periodString . '.pdf'; // Perbaikan encoding filename
            return $pdf->download($filename);
        } finally {
            App::setLocale($originalLocale);
        }
    }

    /**
     * Mengekspor detail laporan perusahaan dalam format CSV.
     */
    public function exportDetailCsv(Request $request, Perusahaan $perusahaan)
    {
        $lang = $request->input('lang', App::getLocale());

        $originalLocale = App::getLocale(); // Simpan locale asli
        App::setLocale($lang); // Atur locale untuk PDF

        // Otorisasi: Pastikan user 'perusahaan' hanya bisa mengekspor CSV perusahaan mereka sendiri
        $user = Auth::user();
        if ($user && $user->hasRole('Perusahaan')) {
            $userPerusahaan = $user->perusahaan;
            if (!$userPerusahaan || $userPerusahaan->id !== $perusahaan->id) {
                App::setLocale($originalLocale);
                abort(403, 'Anda tidak diizinkan mengekspor laporan perusahaan ini.');
            }
        }
        $this->checkAuthorization(auth()->user(), ['report.export_detail']); // Panggilan tetap sama

        try {
            $bulan = (int) $request->input('bulan', date('m'));
            $tahun = (int) $request->input('tahun', date('Y'));

            $detailPerhitungans = HasilPerhitungan::with(['user', 'transportasi', 'bahanBakar', 'biaya'])
                ->join('users', 'hasil_perhitungans.user_id', '=', 'users.id')
                ->join('karyawans', 'users.id', '=', 'karyawans.user_id')
                ->where('karyawans.perusahaan_id', $perusahaan->id)
                ->whereMonth('hasil_perhitungans.tanggal', $bulan)
                ->whereYear('hasil_perhitungans.tanggal', $tahun)
                ->orderBy('hasil_perhitungans.tanggal', 'asc')
                ->get();

            $filename = 'detail_laporan_emisi_' . mb_convert_encoding($perusahaan->nama, 'UTF-8', 'UTF-8') . '_' . Carbon::create()->month($bulan)->translatedFormat('F_Y') . '.csv';
            $handle = fopen('php://temp', 'w+');

            fputcsv($handle, [
                'No', 'Tanggal', 'Karyawan', 'Metode', 'Jenis (BB/Transportasi/Biaya)',
                'Nilai Input', 'Jumlah Orang', 'Emisi (kg CO2)', 'Biaya Terkait (Rp)', 'Rute'
            ]);

            $no = 1;
            foreach ($detailPerhitungans as $perhitungan) {
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

                fputcsv($handle, [
                    $no++,
                    $perhitungan->tanggal->format('Y-m-d H:i'),
                    mb_convert_encoding($perhitungan->user->name ?? 'N/A', 'UTF-8', 'UTF-8'),
                    mb_convert_encoding($perhitungan->metode, 'UTF-8', 'UTF-8'),
                    mb_convert_encoding($jenis, 'UTF-8', 'UTF-8'),
                    $nilaiInputFormatted,
                    $perhitungan->jumlah_orang,
                    number_format($perhitungan->hasil_emisi, 2, '.', ''),
                    number_format($perhitungan->biaya->factorEmisi ?? 0, 0, '.', ''),
                    mb_convert_encoding($perhitungan->titik_awal . ' - ' . $perhitungan->titik_tujuan, 'UTF-8', 'UTF-8')
                ]);
            }

            rewind($handle);
            $contents = stream_get_contents($handle);
            fclose($handle);

            return response($contents)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } finally {
            App::setLocale($originalLocale);
        }
    }

    /**
     * Mengekspor detail laporan perusahaan dalam format Excel.
     */
    public function exportDetailExcel(Request $request, Perusahaan $perusahaan)
    {
        $lang = $request->input('lang', App::getLocale());

        $originalLocale = App::getLocale(); // Simpan locale asli
        App::setLocale($lang); // Atur locale untuk PDF

        // Otorisasi: Pastikan user 'perusahaan' hanya bisa mengekspor Excel perusahaan mereka sendiri
        $user = Auth::user();
        if ($user && $user->hasRole('Perusahaan')) {
            $userPerusahaan = $user->perusahaan;
            if (!$userPerusahaan || $userPerusahaan->id !== $perusahaan->id) {
                App::setLocale($originalLocale);
                abort(403, 'Anda tidak diizinkan mengekspor laporan perusahaan ini.');
            }
        }
        $this->checkAuthorization(auth()->user(), ['report.export_detail']); // Panggilan tetap sama

        try {
            $bulan = (int) $request->input('bulan', date('m'));
            $tahun = (int) $request->input('tahun', date('Y'));

            $filename = 'detail_laporan_emisi_' . mb_convert_encoding($perusahaan->nama, 'UTF-8', 'UTF-8') . '_' . Carbon::create()->month($bulan)->translatedFormat('F_Y') . '.xlsx'; // Perbaikan encoding filename

            return Excel::download(new LaporanDetailPerusahaanExport($perusahaan->id, $bulan, $tahun), $filename);
        } finally {
            App::setLocale($originalLocale);
        }
    }

    /**
     * Mengekspor laporan ringkasan dalam format Excel.
     * Akan difilter berdasarkan perusahaan jika user adalah 'perusahaan'.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel(Request $request)
    {
        $this->checkAuthorization(auth()->user(), ['report.export']); // Otorisasi untuk ekspor laporan

        $lang = $request->input('lang', App::getLocale());
        $originalLocale = App::getLocale();
        App::setLocale($lang);

        $user = Auth::user();
        $perusahaanIdFilter = null;
        $relatedUserIds = [];

        if ($user && $user->hasRole('Perusahaan')) {
            $userPerusahaan = $user->perusahaan;
            if ($userPerusahaan) {
                $perusahaanIdFilter = $userPerusahaan->id;
                $relatedUserIds = $userPerusahaan->getAllRelatedUserIds();
            } else {
                App::setLocale($originalLocale);
                abort(403, 'Anda tidak terhubung dengan perusahaan manapun.');
            }
        }

        try {
            $bulan = (int) $request->input('bulan', date('m'));
            $tahun = (int) $request->input('tahun', date('Y'));
            $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;

            // Menggunakan logika agregasi yang sama dengan index() untuk konsistensi
            $aggregatedDataQuery = HasilPerhitungan::select(
                            'karyawans.perusahaan_id',
                            DB::raw('SUM(hasil_perhitungans.hasil_emisi) as total_emisi'),
                            DB::raw('SUM(biayas.factorEmisi) as total_biaya_terkait'),
                            DB::raw('COUNT(hasil_perhitungans.id) as total_perjalanan')
                        )
                        ->join('users', 'hasil_perhitungans.user_id', '=', 'users.id')
                        ->join('karyawans', 'users.id', '=', 'karyawans.user_id')
                        ->leftJoin('biayas', 'hasil_perhitungans.biaya_id', '=', 'biayas.id')
                        ->whereMonth('hasil_perhitungans.tanggal', $bulan)
                        ->whereYear('hasil_perhitungans.tanggal', $tahun);

            if (!empty($relatedUserIds)) {
                $aggregatedDataQuery->whereIn('hasil_perhitungans.user_id', $relatedUserIds);
            }

            $aggregatedData = $aggregatedDataQuery->groupBy('karyawans.perusahaan_id')
                ->get()
                ->keyBy('perusahaan_id');

            $perusahaans = Perusahaan::when($perusahaanIdFilter, function ($query) use ($perusahaanIdFilter) {
                    $query->where('id', $perusahaanIdFilter);
                })
                ->get()
                ->map(function($perusahaan) use ($aggregatedData, $daysInMonth) {
                    $perusahaan->total_emisi_filtered = 0;
                    $perusahaan->total_biaya_filtered = 0;
                    $perusahaan->total_perjalanan = 0;
                    $perusahaan->rata_rata_harian = 0;

                    if ($aggregatedData->has($perusahaan->id)) {
                        $data = $aggregatedData->get($perusahaan->id);
                        $perusahaan->total_emisi_filtered = $data->total_emisi;
                        $perusahaan->total_biaya_filtered = $data->total_biaya_terkait;
                        $perusahaan->total_perjalanan = $data->total_perjalanan;
                        if ($daysInMonth > 0) {
                            $perusahaan->rata_rata_harian = $data->total_emisi / $daysInMonth;
                        }
                    }
                    return $perusahaan;
                });

            $exportData = [];
            $exportData[] = [
                'No',
                'Nama Perusahaan',
                'Total Emisi (kg CO2)',
                'Rata-rata Harian (kg CO2)',
                'Banyak Perjalanan',
                'Periode'
            ];

            $no = 1;
            foreach ($perusahaans as $perusahaan) {
                $exportData[] = [
                    $no++,
                    mb_convert_encoding($perusahaan->nama, 'UTF-8', 'UTF-8'),
                    number_format($perusahaan->total_emisi_filtered, 2, '.', ''),
                    number_format($perusahaan->rata_rata_harian, 2, '.', ''),
                    $perusahaan->total_perjalanan,
                    Carbon::createFromDate($tahun, $bulan)->translatedFormat('F Y')
                ];
            }

            $periodString = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F_Y');
            $fileName = 'laporan_emisi_perusahaan_' . $periodString . '.xlsx';

            // Menggunakan anonymous class untuk export data array
            return Excel::download(new class($exportData) implements FromArray, WithHeadings {
                protected $data;

                public function __construct(array $data)
                {
                    // Baris pertama dari $data adalah header, jadi kita pisahkan
                    $this->headings = array_shift($data);
                    $this->data = $data;
                }

                public function array(): array
                {
                    return $this->data;
                }

                public function headings(): array
                {
                    return $this->headings;
                }
            }, $fileName);

        } finally {
            App::setLocale($originalLocale);
        }
    }
}
