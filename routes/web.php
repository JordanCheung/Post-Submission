<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;

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

Route::get('/', [IndexController::class, 'index']);
Route::get('/home', [IndexController::class, 'index'])->name('home');
Route::get('/dashboard', [IndexController::class, 'dashboard'])->name('dashboard')->middleware('auth:dashboard');

Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/authorize', [LoginController::class, 'callback']);
Route::get('/user', [DashboardController::class, 'user'])->name('user');
Route::get('/posts', [DashboardController::class, 'posts'])->name('posts');

Route::post('/posts/create', [DashboardController::class, 'create']);
Route::get('/posts/create', [DashboardController::class, 'redirectDashboard']);