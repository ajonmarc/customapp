<?php

use App\Http\Controllers\Superadmin\DashboardController;
use App\Http\Controllers\Superadmin\UserController;
use App\Http\Controllers\Superadmin\RoleController;
use App\Http\Controllers\Superadmin\PermissionController;
use App\Http\Controllers\Superadmin\DocumentationController;

use Illuminate\Support\Facades\Route;

Route::middleware('role:Superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('documentation', [DocumentationController::class, 'index'])->name('documentation');  

    Route::filterableResource('users', UserController::class);
    Route::delete('users-bulk-destroy', [UserController::class, 'bulkDestroy'])->name('users.bulk.destroy');

    Route::filterableResource('roles', RoleController::class);
    Route::delete('roles-bulk-destroy', [RoleController::class, 'bulkDestroy'])->name('roles.bulk-destroy');

    Route::filterableResource('permissions', PermissionController::class);
    Route::delete('permissions-bulk-destroy', [PermissionController::class, 'bulkDestroy'])->name('permissions.bulk-destroy');
});
