<?php

declare (strict_types = 1);

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Backend\{
    ActionLogController,
    BahanBakarController,
    BiayaController,
    DashboardController,
    FeedbackController,
    HasilPerhitunganController,
    KaryawanController,
    TransportasiController,
    LocaleController,
    ModulesController,
    PermissionsController,
    PerusahaanController,
    ProfilesController,
    RolesController,
    SettingsController,
    StrategiController,
    TranslationController,
    UserLoginAsController,
    UsersController,
    KonsultasiController
};
use App\Http\Controllers\Frontend\GuideController as FrontendGuideController; // Alias untuk controller frontend
use App\Http\Controllers\Backend\GuideController as BackendGuideController;
use App\Http\Controllers\FeedbacksController;
use App\Http\Controllers\Backend\FeedbackController as BackendFeedbackController; // Alias untuk controller backend
use App\Http\Controllers\Backend\LaporanController;



Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');
//panduan
Route::get('/panduan', [FrontendGuideController::class, 'index'])->name('guides.index');
// Feedback

Route::post('/feedback', [FeedbacksController::class, 'store'])->name('feedback.store');
// Auth routes
Auth::routes(['verify' => true]);

// Dashboard (all authenticated users can access this prefix)
Route::middleware(['auth', 'verified'])
    ->prefix('dashboard')->as('admin.')
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        /**
     * Admin / Superadmin routes only
     */
        Route::middleware(['role:admin|superadmin'])->group(function () {
            Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.exportPdf');
            Route::get('/laporan/export-csv', [LaporanController::class, 'exportCsv'])->name('laporan.exportCsv');
            Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.exportExcel');
            Route::get('/laporan/{perusahaan}/detail', [LaporanController::class, 'showDetail'])->name('laporan.detail');
            Route::get('/laporan/{perusahaan}/detail/export-pdf', [LaporanController::class, 'exportDetailPdf'])->name('laporan.detail.exportPdf');
            Route::get('/laporan/{perusahaan}/detail/export-csv', [LaporanController::class, 'exportDetailCsv'])->name('laporan.detail.exportCsv');
            Route::get('/laporan/{perusahaan}/detail/export-excel', [LaporanController::class, 'exportDetailExcel'])->name('laporan.detail.exportExcel');
            Route::resources([
                'bahan-bakar'  => BahanBakarController::class,
                'transportasi' => TransportasiController::class,
                'perhitungan'  => HasilPerhitunganController::class,
                'biaya'        => BiayaController::class,
                'strategis'    => StrategiController::class,
                'perusahaans'  => PerusahaanController::class,
                'karyawans'    => KaryawanController::class,
                'roles'        => RolesController::class,
                'users'        => UsersController::class,
                'laporan'      => LaporanController::class,
            ]);
            Route::post('konsultasis/{konsultasi}/approve', [KonsultasiController::class, 'approve'])->name('konsultasis.approve');

            Route::resource('konsultasis', KonsultasiController::class);

            // Rute Manajemen Panduan (Admin/Superadmin)
            // URL: /dashboard/guides
            // Nama: admin.guides.*
            Route::prefix('guides')->name('guides.')->group(function () {
                Route::get('/', [BackendGuideController::class, 'index'])->name('index');
                Route::get('/create', [BackendGuideController::class, 'create'])->name('create');
                Route::post('/', [BackendGuideController::class, 'store'])->name('store');
                Route::get('/{guide}/edit', [BackendGuideController::class, 'edit'])->name('edit');
                Route::put('/{guide}', [BackendGuideController::class, 'update'])->name('update');
                Route::delete('/{guide}', [BackendGuideController::class, 'destroy'])->name('destroy');
            });

            //feedback
            Route::resource('feedbacks', BackendFeedbackController::class)->only(['index', 'show', 'destroy']);

            Route::get('/permissions', [PermissionsController::class, 'index'])->name('permissions.index');
            Route::get('/permissions/{id}', [PermissionsController::class, 'show'])->name('permissions.show');

            Route::get('/modules', [ModulesController::class, 'index'])->name('modules.index');
            Route::post('/modules/upload', [ModulesController::class, 'upload'])->name('modules.upload');
            Route::post('/modules/toggle-status/{module}', [ModulesController::class, 'toggleStatus'])->name('modules.toggle-status');
            Route::delete('/modules/{module}', [ModulesController::class, 'destroy'])->name('modules.delete');

            Route::get('users/{id}/login-as', [UserLoginAsController::class, 'loginAs'])->name('users.login-as');
            Route::post('users/switch-back', [UserLoginAsController::class, 'switchBack'])->name('users.switch-back');

            Route::get('/action-log', [ActionLogController::class, 'index'])->name('actionlog.index');
        });

        /**
     * Perusahaan only
     */
        Route::middleware(['role:perusahaan'])->group(function () {
            Route::get('/perusahaan', fn() => view('dashboard.perusahaan'))->name('dashboard.perusahaan');
            // Tambahkan route lain untuk perusahaan di sini jika perlu
            Route::get('/pehitungan', [HasilPerhitunganController::class, 'index'])->name('perhitungan.index');
            Route::post('/perhitungan', [HasilPerhitunganController::class, 'store'])->name('perhitungan.store');
            Route::get('/perhitungan/{id}', [HasilPerhitunganController::class, 'show'])->name('perhitungan.show');
            Route::put('/perhitungan/{id}', [HasilPerhitunganController::class, 'update'])->name('perhitungan.update');
        });

        /**
     * Karyawan only
     */
        Route::middleware(['role:karyawan'])->group(function () {
            Route::get('/karyawan', fn() => view('dashboard.karyawan'))->name('dashboard.karyawan');
            // Tambahkan route lain untuk karyawan di sini jika perlu
            Route::resources(['perhitungan'  => HasilPerhitunganController::class,]);
        });

        /**
     * Shared by all authenticated roles (admin, perusahaan, karyawan)
     */
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');

        Route::get('/translations', [TranslationController::class, 'index'])->name('translations.index');
        Route::post('/translations', [TranslationController::class, 'update'])->name('translations.update');
        Route::post('/translations/create', [TranslationController::class, 'create'])->name('translations.create');
    });

//Route untuk strategi
Route::middleware(['role:admin|superadmin'])
    ->resource('strategis', StrategiController::class)
    ->except(['index', 'show']);

// Strategi - Perusahaan & Karyawan hanya bisa lihat
Route::middleware(['role:perusahaan|karyawan'])
    ->prefix('strategis')
    ->name('strategis.')
    ->group(function () {
        Route::get('/', [StrategiController::class, 'index'])->name('index');
        Route::get('/{strategi}', [StrategiController::class, 'show'])->name('show');
    });
//Route konsultasi
Route::middleware(['role:perusahaan'])->group(function () {
    // Perusahaan/Karyawan bisa melihat daftar konsultasi mereka
    Route::get('/konsultasis', [KonsultasiController::class, 'index'])->name('konsultasis.index');
    // Perusahaan/Karyawan bisa melihat detail konsultasi mereka
    Route::get('/konsultasis/{konsultasi}', [KonsultasiController::class, 'show'])->name('konsultasis.show');
    // Perusahaan/Karyawan bisa mengajukan konsultasi baru (form dan store)
    Route::get('/konsultasis/create', [KonsultasiController::class, 'create'])->name('konsultasis.create');
    Route::post('/konsultasis', [KonsultasiController::class, 'store'])->name('konsultasis.store');
});

/**
 * Profile routes (untuk semua user login)
 */
Route::middleware('auth')
    ->prefix('profile')->as('profile.')
    ->group(function () {
        Route::get('/edit', [ProfilesController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfilesController::class, 'update'])->name('update');
    });

/**
 * Locale switcher
 */
Route::get('/locale/{lang}', [LocaleController::class, 'switch'])->name('locale.switch');
