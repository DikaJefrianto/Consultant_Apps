<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Charts\UserChartService;
use App\Services\LanguageService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Perusahaan; // Pastikan model ini ada
use App\Models\Karyawan;   // Pastikan model ini ada
use App\Models\HasilPerhitungan; // Pastikan model ini ada
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function __construct(
        private readonly UserChartService $userChartService,
        private readonly LanguageService $languageService
    ) {
    }

    public function index()
    {
        $user = Auth::user();

        // Pastikan pengguna memiliki izin untuk melihat dashboard
        $this->checkAuthorization($user, ['dashboard.view']);

        // Inisialisasi variabel emission_chart_data secara default untuk menghindari undefined variable
        // Ini akan ditimpa jika ada perhitungan spesifik untuk peran tertentu
        $emission_chart_data = ['labels' => [], 'data' => []];

        // Logika untuk menampilkan dashboard berdasarkan peran
        if ($user->hasRole('Superadmin') || $user->hasRole('Admin')) {
            // Dashboard untuk Superadmin dan Admin (mereka akan melihat dashboard yang sama)
            $total_users = number_format(User::count());
            $total_roles = number_format(Role::count());
            $total_permissions = number_format(Permission::count());
            $total_perusahaan = number_format(Perusahaan::count()); // Ambil total perusahaan

            $languages = [
                'total' => number_format(count($this->languageService->getLanguages())),
                'active' => number_format(count($this->languageService->getActiveLanguages())),
            ];

            $user_growth_data = $this->userChartService->getUserGrowthData(
                request()->get('chart_filter_period', 'last_12_months')
            )->getData(true);

            $user_history_data = $this->userChartService->getUserHistoryData();

            // === Logika untuk Grafik Tren Emisi Global (Admin/Superadmin) ===
            $emission_data_raw = HasilPerhitungan::selectRaw('
                    DATE_FORMAT(tanggal, "%Y-%m") as month_year,
                    SUM(hasil_emisi) as total_emission
                ')
                ->groupBy('month_year')
                ->orderBy('month_year', 'asc') // Urutkan berdasarkan bulan/tahun
                ->where('tanggal', '>=', Carbon::now()->subMonths(11)->startOfMonth()) // Ambil 12 bulan terakhir
                ->get();

            $emission_labels = [];
            $emission_values = [];

            // Isi label untuk 12 bulan terakhir, bahkan jika tidak ada data untuk bulan tertentu
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $emission_labels[] = $date->translatedFormat('M Y'); // Format seperti 'Jan 2023', 'Feb 2023'
                $emission_values[] = 0; // Default ke 0 jika tidak ada data
            }

            // Isi nilai emisi yang sebenarnya ke dalam array
            foreach ($emission_data_raw as $data) {
                $dateFromDb = Carbon::parse($data->month_year);
                $formattedLabel = $dateFromDb->translatedFormat('M Y');

                // Cari indeks yang cocok di emission_labels
                $index = array_search($formattedLabel, $emission_labels);
                if ($index !== false) {
                    $emission_values[$index] = $data->total_emission;
                }
            }
            $emission_chart_data = [
                'labels' => $emission_labels,
                'data' => $emission_values
            ];
            // === Akhir Logika untuk Grafik Tren Emisi Global ===

            return view(
                'backend.pages.dashboard.index', // Gunakan nama view yang telah disatukan
                [
                    'total_users' => $total_users,
                    'total_roles' => $total_roles,
                    'total_permissions' => $total_permissions,
                    'languages' => $languages,
                    'user_growth_data' => $user_growth_data,
                    'user_history_data' => $user_history_data,
                    'total_perusahaan' => $total_perusahaan, // Data baru
                    'emission_chart_data' => $emission_chart_data // Dilemparkan ke view
                ]
            );
        } elseif ($user->hasRole('Perusahaan')) {
            // Dashboard untuk peran Perusahaan
            $perusahaan = $user->perusahaan; // Asumsi relasi 'perusahaan' ada di model User

            if (!$perusahaan) {
                // Tangani kasus di mana pengguna perusahaan tidak memiliki data perusahaan terkait
                return view('backend.pages.dashboard.perusahaan_dashboard', [
                    'message' => 'Data perusahaan tidak ditemukan untuk pengguna ini.',
                    'emission_chart_data' => ['labels' => [], 'data' => []] // Pastikan variabel didefinisikan
                ]);
            }

            $total_karyawan = $perusahaan->karyawans()->count();

            // Dapatkan ID pengguna yang terkait dengan perusahaan ini (pengguna perusahaan itu sendiri + pengguna karyawannya)
            $karyawan_user_ids = $perusahaan->karyawans->pluck('user_id')->toArray();
            $all_related_user_ids = array_unique(array_merge([$user->id], $karyawan_user_ids));

            // Ambil perhitungan emisi untuk semua pengguna terkait dalam bulan/tahun saat ini
            $perhitungan_emisi_perusahaan = HasilPerhitungan::whereIn('user_id', $all_related_user_ids)
                                            ->whereMonth('tanggal', now()->month)
                                            ->whereYear('tanggal', now()->year)
                                            ->get();

            $total_perjalanan_karyawan = $perhitungan_emisi_perusahaan->count();
            $total_perhitungan_karyawan = $total_perjalanan_karyawan; // Biasanya, satu perhitungan per perjalanan
            $total_emisi_perusahaan = $perhitungan_emisi_perusahaan->sum('hasil_emisi');

            $aktivitas_terbaru_karyawan = HasilPerhitungan::with('user') // Eager load user untuk menampilkan nama
                                            ->whereIn('user_id', $all_related_user_ids)
                                            ->latest('tanggal')
                                            ->take(10)
                                            ->get();

            // === Logika untuk Grafik Tren Emisi Perusahaan ===
            $emission_data_company_raw = HasilPerhitungan::whereIn('user_id', $all_related_user_ids)
                ->selectRaw('
                    DATE_FORMAT(tanggal, "%Y-%m") as month_year,
                    SUM(hasil_emisi) as total_emission
                ')
                ->groupBy('month_year')
                ->orderBy('month_year', 'asc')
                ->where('tanggal', '>=', Carbon::now()->subMonths(11)->startOfMonth())
                ->get();

            $emission_labels_company = [];
            $emission_values_company = [];

            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $emission_labels_company[] = $date->translatedFormat('M Y');
                $emission_values_company[] = 0;
            }

            foreach ($emission_data_company_raw as $data) {
                $dateFromDb = Carbon::parse($data->month_year);
                $formattedLabel = $dateFromDb->translatedFormat('M Y');
                $index = array_search($formattedLabel, $emission_labels_company);
                if ($index !== false) {
                    $emission_values_company[$index] = $data->total_emission;
                }
            }
            $emission_chart_data = [ // Variabel ini sekarang didefinisikan untuk Perusahaan
                'labels' => $emission_labels_company,
                'data' => $emission_values_company
            ];
            // === Akhir Logika untuk Grafik Tren Emisi Perusahaan ===

            return view(
                'backend.pages.dashboard.perusahaan_dashboard',
                compact(
                    'perusahaan',
                    'total_karyawan',
                    'total_perjalanan_karyawan',
                    'total_perhitungan_karyawan',
                    'total_emisi_perusahaan',
                    'aktivitas_terbaru_karyawan',
                    'emission_chart_data' // Dilemparkan ke view
                )
            );
        } elseif ($user->hasRole('Karyawan')) {
            // Dashboard untuk peran Karyawan
            $karyawan = $user->karyawan; // Asumsi relasi 'karyawan' ada di model User

            if (!$karyawan) {
                // Tangani kasus di mana pengguna karyawan tidak memiliki data karyawan terkait
                return view('backend.pages.dashboard.karyawan_dashboard', [
                    'message' => 'Data karyawan tidak ditemukan untuk pengguna ini.',
                    'emission_chart_data' => ['labels' => [], 'data' => []] // Pastikan variabel didefinisikan
                ]);
            }

            // Data spesifik untuk karyawan ini
            $total_perjalanan_karyawan = HasilPerhitungan::where('user_id', $user->id)->count();
            $total_emisi_karyawan = HasilPerhitungan::where('user_id', $user->id)
                                    ->whereMonth('tanggal', now()->month)
                                    ->whereYear('tanggal', now()->year)
                                    ->sum('hasil_emisi');

            $aktivitas_terbaru_karyawan = HasilPerhitungan::where('user_id', $user->id)
                                            ->latest('tanggal')
                                            ->take(10)
                                            ->get();

            // === Logika untuk Grafik Tren Emisi Karyawan ===
            $emission_data_employee_raw = HasilPerhitungan::where('user_id', $user->id)
                ->selectRaw('
                    DATE_FORMAT(tanggal, "%Y-%m") as month_year,
                    SUM(hasil_emisi) as total_emission
                ')
                ->groupBy('month_year')
                ->orderBy('month_year', 'asc')
                ->where('tanggal', '>=', Carbon::now()->subMonths(11)->startOfMonth())
                ->get();

            $emission_labels_employee = [];
            $emission_values_employee = [];

            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $emission_labels_employee[] = $date->translatedFormat('M Y');
                $emission_values_employee[] = 0;
            }

            foreach ($emission_data_employee_raw as $data) {
                $dateFromDb = Carbon::parse($data->month_year);
                $formattedLabel = $dateFromDb->translatedFormat('M Y');
                $index = array_search($formattedLabel, $emission_labels_employee);
                if ($index !== false) {
                    $emission_values_employee[$index] = $data->total_emission;
                }
            }
            $emission_chart_data = [ // Variabel ini sekarang didefinisikan untuk Karyawan
                'labels' => $emission_labels_employee,
                'data' => $emission_values_employee
            ];
            // === Akhir Logika untuk Grafik Tren Emisi Karyawan ===

            return view(
                'backend.pages.dashboard.karyawan_dashboard',
                compact(
                    'karyawan',
                    'total_perjalanan_karyawan',
                    'total_emisi_karyawan',
                    'aktivitas_terbaru_karyawan',
                    'emission_chart_data' // Dilemparkan ke view
                )
            );
        } else {
            // Dashboard default atau halaman error jika peran tidak dikenali
            return view('backend.pages.dashboard.karyawan_dashboard', [
                'emission_chart_data' => $emission_chart_data, // Dilemparkan data kosong
                'message' => 'Akses dashboard tidak dikenali.'
            ]);
        }
    }
}
