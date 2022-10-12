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
  Route::any('/edit/{id}', [App\Http\Controllers\DataController::class, 'edit'])->name('url.edit');
  Route::delete('/delete', [App\Http\Controllers\DataController::class, 'delete'])->name('url.delete');
});

//ACT ATT
Route::group(['prefix' => 'ATT','middleware' => ['auth', 'role:SD,ST']], function () {
  Route::any('/', [App\Http\Controllers\AttController::class, 'index'])->name('att.index');
  Route::get('/data', [App\Http\Controllers\AttController::class, 'data'])->name('att.data');
  Route::any('/edit/{id}', [App\Http\Controllers\AttController::class, 'edit'])->name('att.edit');
  Route::delete('/delete', [App\Http\Controllers\AttController::class, 'delete'])->name('att.delete');
  Route::get('/{id}/{token}', [App\Http\Controllers\AttController::class, 'print'])->name('att.print');
});

//MT ATT
Route::group(['prefix' => 'MT','middleware' => ['auth','role:ST']], function () {
  Route::any('/', [App\Http\Controllers\MtController::class, 'index'])->name('mt.index');
  Route::get('/data', [App\Http\Controllers\MtController::class, 'data'])->name('mt.data');
  Route::any('/edit/{id}', [App\Http\Controllers\MtController::class, 'edit'])->name('mt.edit');
  Route::delete('/delete', [App\Http\Controllers\MtController::class, 'delete'])->name('mt.delete');
});

//QR-JGU
Route::group(['prefix' => 'QR','middleware' => ['auth']], function () {
  Route::get('/', [App\Http\Controllers\QrController::class, 'index'])->name('qr.index');
});


//Rest API QR-JGU
Route::get('/qrcode', [App\Http\Controllers\QrController::class, 'qrcode'])->name('qrcode');
//letakkan di paling bawah
Route::get('/{id}', [App\Http\Controllers\QrController::class, 'url'])->name('url.url');