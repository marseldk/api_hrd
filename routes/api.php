<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('logon',[App\Http\Controllers\ApiController::class, 'logon']);
Route::post('absen',[App\Http\Controllers\ApiController::class, 'absen']);
Route::get('getKaryawan',[App\Http\Controllers\ApiController::class, 'getKaryawan']);
Route::post('addKaryawan',[App\Http\Controllers\ApiController::class, 'addKaryawan']);
Route::delete('/deleteKaryawan/{id}',[App\Http\Controllers\ApiController::class, 'deleteKaryawan']);
Route::post('updateKaryawan',[App\Http\Controllers\ApiController::class, 'updateKaryawan']);
Route::get('getData',[App\Http\Controllers\ApiController::class, 'getData']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
