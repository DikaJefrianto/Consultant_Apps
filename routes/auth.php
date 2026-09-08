<?php

use App\Http\Controllers\Backend\Auth\ForgotPasswordController;
use App\Http\Controllers\Backend\Auth\LoginController;
use App\Http\Controllers\Backend\Auth\ResetPasswordController;
/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| Authentication related routes.
|
*/

// Public User authentication routes.
Auth::routes();

// User authentication routes.
Route::group(['prefix' => '', 'as' => 'admin.', 'middleware' => 'guest'], function () {
});

// Authenticated routes.
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    // Login Routes.
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login/submit', [LoginController::class, 'login'])->name('login.submit');


    // Reset Password Routes.
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset.submit');

    // Forget Password Routes.
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/reset-password/submit', [ForgotPasswordController::class, 'reset'])->name('password.update');
    // Logout Routes.
    Route::get('/logout', [LoginController::class, 'logoutForm'])->name('logout');
    Route::post('/logout/submit', [LoginController::class, 'logout'])->name('logout.submit');
});
