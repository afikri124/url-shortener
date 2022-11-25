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
Route::group(['prefix' => 'ATT','middleware' => ['auth', 'role:ST,AD']], function () {
  Route::any('/', [App\Http\Controllers\AttController::class, 'index'])->name('att.index');
  Route::get('/data', [App\Http\Controllers\AttController::class, 'data'])->name('att.data');
  Route::any('/edit/{id}', [App\Http\Controllers\AttController::class, 'edit'])->name('att.edit');
  Route::delete('/delete', [App\Http\Controllers\AttController::class, 'delete'])->name('att.delete');
  Route::get('/list/data/{id}', [App\Http\Controllers\AttController::class, 'list_data'])->name('att.list_data');
  Route::delete('/list/delete', [App\Http\Controllers\AttController::class, 'list_delete'])->name('att.list_delete');
  Route::any('/list/{id}', [App\Http\Controllers\AttController::class, 'list'])->name('att.list');
  Route::get('/print/{id}', [App\Http\Controllers\AttController::class, 'print'])->name('att.print');
});

//User absen menggunakan QR/LInk
Route::any('/A/{id}/{token}', [App\Http\Controllers\HomeController::class, 'attendance'])->name('attendance')->middleware(['auth']);
Route::any('/a/{id}/{token}', [App\Http\Controllers\HomeController::class, 'attendance'])->middleware(['auth']);

//MT ATT
Route::group(['prefix' => 'MT','middleware' => ['auth','role:ST,AD']], function () {
  Route::any('/', [App\Http\Controllers\MtController::class, 'index'])->name('mt.index');
  Route::get('/data', [App\Http\Controllers\MtController::class, 'data'])->name('mt.data');
  Route::any('/edit/{id}', [App\Http\Controllers\MtController::class, 'edit'])->name('mt.edit');
  Route::delete('/delete', [App\Http\Controllers\MtController::class, 'delete'])->name('mt.delete');
  Route::get('/list/data/{id}', [App\Http\Controllers\MtController::class, 'list_data'])->name('mt.list_data');
  Route::delete('/list/delete', [App\Http\Controllers\MtController::class, 'list_delete'])->name('mt.list_delete');
  Route::any('/list/{id}', [App\Http\Controllers\MtController::class, 'list'])->name('mt.list');
  Route::get('/print/{id}', [App\Http\Controllers\MtController::class, 'print'])->name('mt.print');
});

//Rekap Absensi acara/rapat
Route::group(['prefix' => 'attendance','middleware' => ['auth','role:AD,HR']], function () {
  Route::any('/', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
  Route::get('/data', [App\Http\Controllers\AttendanceController::class, 'data'])->name('attendance.data');
  Route::get('/print/{id}', [App\Http\Controllers\AttendanceController::class, 'print'])->name('attendance.print');
});

//JAM KERJA
Route::group(['prefix' => 'WH','middleware' => ['auth','role:ST']], function () {
  Route::any('/', [App\Http\Controllers\WorkHoursController::class, 'wh'])->name('WH.index');
  Route::get('/data', [App\Http\Controllers\WorkHoursController::class, 'wh_data'])->name('WH.data');
  Route::get('/print/{id}', [App\Http\Controllers\WorkHoursController::class, 'wh_print'])->name('WH.print');
  Route::get('/total_h', [App\Http\Controllers\WorkHoursController::class, 'wh_total_h'])->name('WH.total_h');
});
//REKAP JAM KERJA
Route::group(['prefix' => 'WHR','middleware' => ['auth','role:AD,HR']], function () {
  Route::any('/', [App\Http\Controllers\WorkHoursController::class, 'whr'])->name('WHR.index');
  Route::get('/sync', [App\Http\Controllers\WorkHoursController::class, 'whr_sync'])->name('WHR.sync');
  Route::get('/data', [App\Http\Controllers\WorkHoursController::class, 'whr_data'])->name('WHR.data');
  Route::get('/print/{id}', [App\Http\Controllers\WorkHoursController::class, 'whr_print'])->name('WHR.print');
});
//JAM-KERJA tes
Route::get('zk', [\App\Http\Controllers\WorkHoursController::class, 'zk'])->middleware(['auth', 'role:AD']);

//QR-JGU
Route::group(['prefix' => 'QR','middleware' => ['auth','role:ST,SD']], function () {
  Route::get('/', [App\Http\Controllers\QrController::class, 'index'])->name('qr.index');
});

//Notulensi
Route::group(['prefix' => 'MoM','middleware' => ['auth','role:ST']], function () {
  Route::get('/note-taker', [App\Http\Controllers\MoMController::class, 'notetaker'])->name('mom.note-taker');
  Route::get('/note-taker/data', [App\Http\Controllers\MoMController::class, 'notetaker_data'])->name('mom.note-taker_data');
  Route::get('/print/{id}', [App\Http\Controllers\MoMController::class, 'notetaker_print'])->name('mom.note-taker_print');
  Route::post('/note-taker/add', [App\Http\Controllers\MoMController::class, 'notetaker_add'])->name('mom.notetaker_add');
  Route::post('/note-taker/edit', [App\Http\Controllers\MoMController::class, 'notetaker_edit'])->name('mom.notetaker_edit');
  Route::delete('/note-taker/delete', [App\Http\Controllers\MoMController::class, 'notetaker_delete'])->name('mom.notetaker_delete');
  Route::get('/note-taker/{id}/data', [App\Http\Controllers\MoMController::class, 'notetaker_id_data'])->name('mom.notetaker_id_data');
  Route::any('/note-taker/{id}', [App\Http\Controllers\MoMController::class, 'notetaker_id'])->name('mom.notetaker_id');
  Route::post('/list_id', [App\Http\Controllers\MoMController::class, 'list_id'])->name('mom.list_id');
  Route::get('/PIC', [App\Http\Controllers\MoMController::class, 'PIC'])->name('mom.PIC');
  Route::get('/PIC/data', [App\Http\Controllers\MoMController::class, 'PIC_data'])->name('mom.PIC_data');
  Route::get('/PIC/{id}', [App\Http\Controllers\MoMController::class, 'PIC_id'])->name('mom.PIC_id');
  Route::get('/meeting', [App\Http\Controllers\MoMController::class, 'meeting'])->name('mom.meeting');
  Route::get('/meeting/data', [App\Http\Controllers\MoMController::class, 'meeting_data'])->name('mom.meeting_data');
  Route::get('/meeting/{id}', [App\Http\Controllers\MoMController::class, 'meeting_id'])->name('mom.meeting_id');
  Route::get('/mom_docs', [App\Http\Controllers\MoMController::class, 'mom_docs'])->name('mom.mom_docs');
  Route::delete('/mom_docs/delete', [App\Http\Controllers\MoMController::class, 'mom_docs_delete'])->name('mom.mom_docs_delete');
});

//Pengaturan
Route::group(['prefix' => 'setting','middleware' => ['auth','role:AD']], function () {
  Route::get('account', [App\Http\Controllers\SettingController::class, 'account'])->name('setting_account');
  Route::get('account/data', [App\Http\Controllers\SettingController::class, 'account_data'])->name('setting_account_data');
  Route::any('account/edit/{id}', [App\Http\Controllers\SettingController::class, 'account_edit'])->name('setting_account_edit');
  Route::delete('account/delete', [App\Http\Controllers\SettingController::class, 'account_delete'])->name('setting_account_delete');
});
Route::group(['prefix' => 'setting','middleware' => ['auth','role:HR']], function () {
  Route::get('account_att', [App\Http\Controllers\SettingController::class, 'account_att'])->name('setting_account_att');
  Route::get('account_att/sync', [App\Http\Controllers\SettingController::class, 'account_att_sync'])->name('setting_account_att_sync');
  Route::get('account_att/data', [App\Http\Controllers\SettingController::class, 'account_att_data'])->name('setting_account_att_data');
  Route::any('account_att/edit/{id}', [App\Http\Controllers\SettingController::class, 'account_att_edit'])->name('setting_account_att_edit');
});


Route::get('log-viewers', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->middleware(['auth', 'role:AD']);

//Rest API QR-JGU
Route::get('/qrcode', [App\Http\Controllers\QrController::class, 'qrcode'])->name('qrcode');
//letakkan di paling bawah
Route::get('/{id}', [App\Http\Controllers\QrController::class, 'url'])->name('url.url');