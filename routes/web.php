<?php

use Illuminate\Support\Facades\Route;

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
})->name('index');

Auth::routes([
    'register' => false, // Registration Routes...
  ]);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware(['auth']);

Route::any('/attendance/{id}', [App\Http\Controllers\HomeController::class, 'attendance'])->name('attendance')->middleware(['auth']);

Route::get('/login/sso_klas2/', [App\Http\Controllers\HomeController::class, 'sso_klas2'])->name('sso_klas2');
Route::get('/login/google', [App\Http\Controllers\GoogleController::class, 'redirectToGoogle']);
Route::get('/login/google/callback', [App\Http\Controllers\GoogleController::class, 'handleCallback']);

//URL Shortener
Route::group(['prefix' => 'URL','middleware' => ['auth']], function () {
  Route::any('/', [App\Http\Controllers\DataController::class, 'index'])->name('url.index');
  Route::get('/data', [App\Http\Controllers\DataController::class, 'data'])->name('url.data');
  Route::any('/ubah/{id}', [App\Http\Controllers\DataController::class, 'ubah'])->name('url.ubah');
  Route::delete('/hapus', [App\Http\Controllers\DataController::class, 'hapus'])->name('url.hapus');
});

Route::get('/qrcode', [App\Http\Controllers\QrController::class, 'qrcode'])->name('qrcode');


//Tarok paling bawah
Route::get('/{id}', [App\Http\Controllers\DataController::class, 'url'])->name('url.url');