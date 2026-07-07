<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AuthController;

Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'adminLogin'])->middleware('throttle:6,1')->name('login.submit');
});

Route::middleware(['auth.admin:admin'])->group(function () {
    Route::post('/logout', [AuthController::class, 'adminLogout'])->name('admin.logout');

    Route::controller(AdminDashboardController::class)
        ->group(function () {
            Route::get('/dashboard', 'index')->name('dashboard');
        });
});