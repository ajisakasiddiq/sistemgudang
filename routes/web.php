<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/Login', function () {
    return view('pages.sign-in');
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/User', function () {
        return view('pages.user');
    });
    Route::get('/Mutasi', function () {
        return view('pages.mutasi');
    });
    Route::get('/Barang', function () {
        return view('pages.barang');
    });
});
