<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    require __DIR__.'/superadmin.php';
    require __DIR__.'/admin.php';
    require __DIR__.'/user.php';
});

require __DIR__.'/settings.php';