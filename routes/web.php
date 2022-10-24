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
Route::get('/login/sso_klas2/', [App\Http\Controllers\HomeController::class, 'sso_klas2'])->name('sso_klas2');
Route::get('/login/google', [App\Http\Controllers\GoogleController::class, 'redirectToGoogle']);
Route::get('/login/google/callback', [App\Http\Controllers\GoogleController::class, 'handleCallback']);

//User
Route::group(['middleware' => ['auth']], function () {
  Route::get('/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('user.profile');
  Route::get('/profile/data', [App\Http\Controllers\UserController::class, 'data'])->name('user.data');
  Route::get('/profile/data/{id}', [App\Http\Controllers\UserController::class, 'data_by_id'])->name('user.data_by_id');
  Route::any('/profile/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('user.edit');
  Route::get('/profile/{id}', [App\Http\Controllers\UserController::class, 'profile_by_id'])->name('user.profile_by_id');
});

//URL Shortener
Route::group(['prefix' => 'URL','middleware' => ['auth', 'role:ST,SD']], function () {
  Route::any('/', [App\Http\Controllers\DataController::class, 'index'])->name('url.index');
  Route::get('/data', [App\Http\Controllers\DataController::class, 'data'])->name('url.data');
  Route::any('/edit/{id}', [App\Http\Controllers\DataController::class, 'edit'])->name('url.edit');
  Route::delete('/delete', [App\Http\Controllers\DataController::class, 'delete'])->name('url.delete');
});

//ACT ATT
Route::group(['prefix' => 'ATT','middleware' => ['auth', 'role:ST']], function () {
  Route::any('/', [App\Http\Controllers\AttController::class, 'index'])->name('att.index');
  Route::get('/data', [App\Http\Controllers\AttController::class, 'data'])->name('att.data');
  Route::any('/edit/{id}', [App\Http\Controllers\AttController::class, 'edit'])->name('att.edit');
  Route::delete('/delete', [App\Http\Controllers\AttController::class, 'delete'])->name('att.delete');
  Route::get('/list/data/{id}', [App\Http\Controllers\AttController::class, 'list_data'])->name('att.list_data');
  Route::delete('/list/delete', [App\Http\Controllers\AttController::class, 'list_delete'])->name('att.list_delete');
  Route::any('/list/{id}', [App\Http\Controllers\AttController::class, 'list'])->name('att.list');
  Route::get('/print/{id}', [App\Http\Controllers\AttController::class, 'print'])->name('att.print');
});

//User absen pakai QR
Route::any('/A/{id}/{token}', [App\Http\Controllers\HomeController::class, 'attendance'])->name('attendance')->middleware(['auth']);

//MT ATT
Route::group(['prefix' => 'MT','middleware' => ['auth','role:ST']], function () {
  Route::any('/', [App\Http\Controllers\MtController::class, 'index'])->name('mt.index');
  Route::get('/data', [App\Http\Controllers\MtController::class, 'data'])->name('mt.data');
  Route::any('/edit/{id}', [App\Http\Controllers\MtController::class, 'edit'])->name('mt.edit');
  Route::delete('/delete', [App\Http\Controllers\MtController::class, 'delete'])->name('mt.delete');
  Route::get('/list/data/{id}', [App\Http\Controllers\MtController::class, 'list_data'])->name('mt.list_data');
  Route::delete('/list/delete', [App\Http\Controllers\MtController::class, 'list_delete'])->name('mt.list_delete');
  Route::any('/list/{id}', [App\Http\Controllers\MtController::class, 'list'])->name('mt.list');
  Route::get('/print/{id}', [App\Http\Controllers\MtController::class, 'print'])->name('mt.print');
});

//QR-JGU
Route::group(['prefix' => 'QR','middleware' => ['auth','role:ST,SD']], function () {
  Route::get('/', [App\Http\Controllers\QrController::class, 'index'])->name('qr.index');
});

Route::group(['prefix' => 'setting','middleware' => ['auth','role:AD']], function () {
  Route::get('account', [App\Http\Controllers\SettingController::class, 'account'])->name('setting_account');
  Route::get('account/data', [App\Http\Controllers\SettingController::class, 'account_data'])->name('setting_account_data');
  Route::any('account/edit/{id}', [App\Http\Controllers\SettingController::class, 'account_edit'])->name('setting_account_edit');
});

Route::get('log-viewers', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->middleware(['auth', 'role:AD']);

//Rest API QR-JGU
Route::get('/qrcode', [App\Http\Controllers\QrController::class, 'qrcode'])->name('qrcode');
//letakkan di paling bawah
Route::get('/{id}', [App\Http\Controllers\QrController::class, 'url'])->name('url.url');