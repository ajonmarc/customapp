<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware('role:Admin')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('dashboard', [
            DashboardController::class,
            'index',
        ])->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        Route::get('users/data', [
            UserController::class,
            'getData',
        ])->name('users.data');

        Route::resource('users', UserController::class);

        Route::get('roles/data', [
            RoleController::class,
            'getData',
        ])->name('roles.data'); 


         Route::resource('roles', RoleController::class);
    });