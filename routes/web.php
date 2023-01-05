<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/dashboard', [HomeController::class, 'index'])->name('admin.dashboard')->middleware('admin:admin');
Route::get('/admin/login', [AuthController::class, 'login'])->name('admin.login');
Route::get('/admin/forget-password', [AuthController::class, 'forget_password'])->name('admin.forget-password');
Route::post('/admin/login-submit', [AuthController::class, 'login_submit'])->name('admin.login-submit');
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
Route::post('/admin/forget-password/submit', [AuthController::class, 'forget_password_submit'])->name('admin.forget-password.submit');
Route::get('/admin/reset-password/{token}/{email}', [AuthController::class, 'reset_password'])->name('admin.reset-password');
Route::post('/admin/reset-password/submit', [AuthController::class, 'reset_password_submit'])->name('admin.reset-password.submit');

Route::get('/admin/profile', [ProfileController::class, 'edit_profile'])->name('admin.profile')->middleware('admin:admin');
Route::post('/admin/profile-submit', [ProfileController::class, 'edit_profile_submit'])->name('admin.profile.submit')->middleware('admin:admin');
