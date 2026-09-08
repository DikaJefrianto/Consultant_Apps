<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Api\KaryawanController;
use App\Http\Controllers\Backend\Api\PerusahaanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// API endpoint to get translations for a specific language
Route::get('/translations/{lang}', function (string $lang) {
    $path = resource_path("lang/{$lang}.json");

    if (!file_exists($path)) {
        return response()->json(['error' => 'Language not found'], 404);
    }

    $translations = json_decode(file_get_contents($path), true);
    return response()->json($translations);
});
// Route manual untuk API Perusahaan

Route::get('/perusahaans', [PerusahaanController::class, 'index']);           // Menampilkan semua data
Route::post('/perusahaans', [PerusahaanController::class, 'store']);          // Menambahkan data baru
Route::get('/detailperusahaans/{id}', [PerusahaanController::class, 'show']);       // Menampilkan detail data berdasarkan ID
Route::put('/editperusahaans/{id}', [PerusahaanController::class, 'update']);     // Memperbarui data berdasarkan ID
Route::delete('/deleteperusahaans/{id}', [PerusahaanController::class, 'destroy']); // Menghapus data berdasarkan ID

Route::get('/karyawans', [KaryawanController::class, 'index']);           // Menampilkan semua data
Route::post('/karyawans', [KaryawanController::class, 'store']);          // Menambahkan data baru
Route::get('/detailkaryawans/{id}', [KaryawanController::class, 'show']);       // Menampilkan detail data berdasarkan ID
Route::put('/editkaryawans/{id}', [KaryawanController::class, 'update']);     // Memperbarui data berdasarkan ID
Route::delete('/deletekaryawans/{id}', [KaryawanController::class, 'destroy']); // Menghapus data berdasarkan ID
