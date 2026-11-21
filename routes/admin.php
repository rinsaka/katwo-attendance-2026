<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

// Admin 認証不要
Route::group(['prefix' => 'admin'], function() {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
});
