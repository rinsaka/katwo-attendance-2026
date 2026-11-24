<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\MenuController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/confirm_delete', [DashboardController::class, 'confirm_delete'])->name('confirm_delete');
    Route::get('/dashboard/list', [DashboardController::class, 'all_act'])->name('all_act');
    Route::get('/dashboard/mail', [DashboardController::class, 'mail_footer'])->name('mail_footer');
    Route::get('/dashboard/{year}/{month}', [DashboardController::class, 'show'])->name('show');
    Route::get('/dashboard/{year}/{month}/create', [DashboardController::class, 'create'])->name('create');
    Route::get('/dashboard/{year}/{month}/{aid}/edit', [DashboardController::class, 'edit'])->name('edit');
    Route::post('/dashboard', [DashboardController::class, 'store'])->name('store');
    Route::patch('/dashboard', [DashboardController::class, 'update'])->name('update');
    Route::delete('/dashboard', [DashboardController::class, 'destroy'])->name('destroy');


    // Route::get('/menu/create', 'MenusController@create');
    Route::get('/menu/create', [MenuController::class, 'create'])->name('menu_create');


    // Route::get('/menu/{id}', 'MenusController@show');
    Route::get('/menu/{id}', [MenuController::class, 'show'])->name('menu_show');

    // Route::get('/menu/{id}/mail', 'MenusController@mail');
    Route::get('/menu/{id}/mail', [MenuController::class, 'mail'])->name('menu_mail');

    // Route::patch('/menu', 'MenusController@update')->name('menu_update');
    Route::patch('/menu', [MenuController::class, 'update'])->name('menu_update');


    // Route::post('/menu', 'MenusController@store')->name('menu_store');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
