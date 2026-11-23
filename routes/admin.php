<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlaceController;
use App\Http\Controllers\Admin\TimeController;
use App\Http\Controllers\Admin\PasswordController;
use App\Http\Controllers\Admin\LoginController;

// // Admin 認証不要
// Route::group(['prefix' => 'admin'], function() {
//     Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
// });


// Admin ログイン画面
Route::get('/admin/login', [LoginController::class, 'create'])->name('admin.login');
// Admin ログイン
Route::post('/admin/login', [LoginController::class, 'store'])->name('admin.login.store');
// Admin ログアウト
Route::post('/admin/logout', [LoginController::class, 'destroy'])
                ->name('admin.logout');

// Admin ログイン後のみアクセス可
Route::middleware('auth:admin')->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/activity/create', [DashboardController::class, 'create'])->name('admin.act_create');
    Route::get('/activity/{aid}', [DashboardController::class, 'edit'])->name('admin.edit');
    Route::patch('/activity', [DashboardController::class, 'update'])->name('admin_act_update');
    Route::post('/activity', [DashboardController::class, 'store'])->name('admin.act_store');
    Route::delete('/activity/{id}', [DashboardController::class, 'destory'])->name('admin.act_destory');

    Route::get('/place', [PlaceController::class, 'index'])->name('admin.place');
    Route::get('/place/create', [PlaceController::class, 'create'])->name('admin.place_create');
    Route::get('/place/{pid}', [PlaceController::class, 'edit'])->name('admin.place_edit');
    Route::delete('/place/{pid}', [PlaceController::class, 'destroy'])->name('admin.place_destroy');
    Route::get('/place/{pid}/delete', [PlaceController::class, 'delete'])->name('admin.place_delete');
    Route::patch('/place', [PlaceController::class, 'update'])->name('admin.place_update');
    Route::post('/place', [PlaceController::class, 'store'])->name('admin.place_store');


    Route::get('/time', [TimeController::class, 'index'])->name('admin.time');
    Route::get('/time/create', [TimeController::class, 'create'])->name('admin.time_create');

    // Route::get('time/create', 'Admin\HomeController@time_create');
    // Route::get('time/{tid}', 'Admin\HomeController@time_edit')->name('admin.time_edit');
    // Route::get('time/{tid}/delete', 'Admin\HomeController@time_delete');
    // Route::patch('time', 'Admin\HomeController@time_update')->name('admin_time_update');

    // Route::post('time', 'Admin\HomeController@time_store')->name('admin_time_store');
    Route::post('/time', [TimeController::class, 'store'])->name('admin.time_store');


    // Route::delete('time/{tid}', 'Admin\HomeController@time_destroy');


    Route::get('/password', [PasswordController::class, 'passwd'])->name('admin.passwd');
    Route::patch('/password', [PasswordController::class, 'passwd_update'])->name('admin.passwd_update');

    Route::get('/userpassword', [PasswordController::class, 'userpasswd'])->name('admin.userpasswd');
    Route::patch('/userpassword', [PasswordController::class, 'userpasswd_update'])->name('admin.userpasswd_update');


    // Route::get('mailinfo', 'Admin\MailinfosController@edit');
    // Route::post('mailinfo', 'Admin\MailinfosController@update')->name('admin.mailinfo');
});
