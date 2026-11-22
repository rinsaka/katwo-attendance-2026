<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
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

    // Route::get('activity/create', 'Admin\HomeController@create');
    // Route::get('activity/{aid}', 'Admin\HomeController@edit');
    Route::get('/activity/{aid}', [DashboardController::class, 'edit'])->name('admin.edit');

    // Route::patch('activity', 'Admin\HomeController@update')->name('admin_act_update');
    // Route::post('activity', 'Admin\HomeController@store')->name('admin_act_store');
    // Route::post('logout', 'Admin\LoginController@logout')->name('admin.logout');
    // Route::delete('activity/{id}', 'Admin\HomeController@destory');
    // Route::get('place', 'Admin\HomeController@place')->name('admin.place');
    // Route::get('place/create', 'Admin\HomeController@place_create');
    // Route::get('place/{pid}', 'Admin\HomeController@place_edit');
    // Route::delete('place/{pid}', 'Admin\HomeController@place_destroy');
    // Route::get('place/{pid}/delete', 'Admin\HomeController@place_delete');
    // Route::patch('place', 'Admin\HomeController@place_update')->name('admin_place_update');
    // Route::post('place', 'Admin\HomeController@place_store')->name('admin_place_store');
    // Route::get('time', 'Admin\HomeController@time')->name('admin.time');
    // Route::get('time/create', 'Admin\HomeController@time_create');
    // Route::get('time/{tid}', 'Admin\HomeController@time_edit')->name('admin.time_edit');
    // Route::get('time/{tid}/delete', 'Admin\HomeController@time_delete');
    // Route::patch('time', 'Admin\HomeController@time_update')->name('admin_time_update');
    // Route::post('time', 'Admin\HomeController@time_store')->name('admin_time_store');
    // Route::delete('time/{tid}', 'Admin\HomeController@time_destroy');
    // Route::get('password', 'Admin\HomeController@passwd');
    // Route::patch('password', 'Admin\HomeController@passwd_update')->name('admin.passwd');
    // Route::get('userpassword', 'Admin\HomeController@userpasswd');
    // Route::patch('userpassword', 'Admin\HomeController@userpasswd_update')->name('admin.userpasswd');
    // Route::get('mailinfo', 'Admin\MailinfosController@edit');
    // Route::post('mailinfo', 'Admin\MailinfosController@update')->name('admin.mailinfo');
});
