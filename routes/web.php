<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Master\MenuController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/login', 301);
Route::get('logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
 */
Route::middleware(['guest'])->group(function () {
    Route::get('login', [LoginController::class, 'login'])->name('login');
    Route::post('login/authenticate', [LoginController::class, 'authenticate'])->name('login.authenticate');
    Route::post('captcha/reload', [LoginController::class, 'reloadCaptcha'])->name('captcha.reload');
});

Route::middleware(['auth'])->group(function () {/*

    /*
    |--------------------------------------------------------------------------
    | Home Routes
    |--------------------------------------------------------------------------
    */
    Route::get('home', function () {
        // dd(session()->get('user')->nik_func);
        return view('pages.home');
    })->name('home');


});
