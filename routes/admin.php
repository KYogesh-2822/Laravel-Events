<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;

Route::controller(AdminDashboardController::class)
    ->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
    });