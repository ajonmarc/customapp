<?php

use App\Http\Controllers\Superadmin\DashboardController;

use Illuminate\Support\Facades\Route;

Route::middleware('role:Superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

});