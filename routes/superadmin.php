<?php

use App\Http\Controllers\Superadmin\DashboardController;
use App\Http\Controllers\Superadmin\UserController;
use App\Http\Controllers\Superadmin\RoleController;
use App\Http\Controllers\Superadmin\PermissionController;

use Illuminate\Support\Facades\Route;

Route::middleware('role:Superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class);
    Route::delete('users-bulk-destroy', [UserController::class, 'bulkDestroy'])->name('users.bulk.destroy');

    Route::get('roles', [RoleController::class, 'roles'])->name('roles');
    Route::get('permissions', [PermissionController::class, 'permissions'])->name('permissions');
});