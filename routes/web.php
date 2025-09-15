<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Operator;
use App\Http\Controllers\Operator\SesiAbsensiController; 


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

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login'); // Halaman login
Route::post('/login', [LoginController::class, 'login']); // Proses login
Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/kegiatan/info/{id}', [App\Http\Controllers\InfoController::class, 'show'])->name('kegiatan.info');

Route::post('/logout', function () {
    Auth::logout();  // Menjalankan proses logout
    return redirect()->route('login');  // Mengarahkan pengguna ke halaman login setelah logout
})->name('logout');


Route::get('/scan/{idmeja}', [Customer\MenuController::class, 'scanQRCode'])->name('customer.scan.qrcode');
Route::post('/konfirmasi', [Customer\MenuController::class, 'konfirmasiPembayaran'])->name('customer.konfirmasi');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function() { return redirect()->route('admin.dashboard'); })->name('redirect.admin.dashboard'); 
    Route::get('/admin/dashboard', [Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/profile', [Admin\ProfileController::class, 'index'])->name('admin.profile');
    Route::get('/admin/kelolauser', [Admin\kelolaUserController::class, 'index'])->name('admin.kelolaUsers');
    Route::post('/admin/kelolauser', [Admin\kelolaUserController::class, 'store'])->name('admin.kelolauser.add');
    Route::post('/admin/kelolauser/{id}', [Admin\kelolaUserController::class, 'delete'])->name('admin.kelolauser.delete');
    Route::put('/admin/kelolauser/update/{id}', [Admin\kelolaUserController::class, 'update'])->name('admin.kelolauser.update');
});


Route::middleware(['auth', 'role:operator'])->group(function () {

    // DASHBOARD
    Route::get('/operator', [Operator\DashboardController::class, 'index'])
        ->name('operator.dashboard');

    // ====== KEGIATAN ======
    Route::prefix('operator/kegiatan')->name('operator.kegiatan.')->group(function () {
        Route::get('/', [Operator\KegiatanController::class, 'index'])->name('index');
        Route::get('/{id}', [Operator\KegiatanController::class, 'detail'])->name('detail');
        Route::post('/store', [Operator\KegiatanController::class, 'store'])->name('store');
        Route::put('/update/{id}', [Operator\KegiatanController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [Operator\KegiatanController::class, 'delete'])->name('delete');
    });

    // ====== SESI absensi / ======
    Route::prefix('operator/kegiatan/{kegiatan}/sesi')->name('operator.kegiatan.sesi.')->group(function () {
        Route::post('/store', [SesiAbsensiController::class, 'store'])->name('store');
        Route::put('/update/{sesi_absensi}', [SesiAbsensiController::class, 'update'])->name('update');
        Route::post('/destroy/{sesi_absensi}', [SesiAbsensiController::class, 'destroy'])->name('destroy');
    });


    // ====== PESERTA ======
    Route::prefix('operator/peserta')->name('operator.peserta.')->group(function () {
        Route::get('/', [Operator\PesertaController::class, 'index'])->name('index');
        Route::get('/{id}', [Operator\PesertaController::class, 'show'])->name('show');
        Route::post('/store', [Operator\PesertaController::class, 'store'])->name('store');
        Route::put('/update/{id}', [Operator\PesertaController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [Operator\PesertaController::class, 'delete'])->name('delete');
        Route::delete('/destroy-all/{kegiatan}', [Operator\PesertaController::class, 'destroyAll'])->name('destroy.all');
        Route::get('/export/{kegiatan}', [Operator\PesertaController::class, 'export'])->name('export');
        Route::get('/export/link-qr/{kegiatan}', [Operator\PesertaController::class, 'exportLinkQr'])->name('export.linkqr');
        Route::post('/import', [Operator\PesertaController::class, 'import'])->name('import');
        Route::get('/download-qrcode/{id}', [Operator\PesertaController::class, 'downloadQRCode'])->name('download_qrcode');
    });


    // ====== ABSENSI / SCAN ======
    Route::prefix('operator/absensi')->name('operator.absensi.')->group(function () {
        Route::get('/export/{kegiatan}/{id_sesi}', [Operator\AbsensiController::class, 'exportAbsensi']) ->name('export'); // <-- Lebih sederhana
        // Menampilkan daftar peserta yang sudah absen di sebuah sesi
        Route::get('/show/{sesi_absensi}', [Operator\AbsensiController::class, 'show'])->name('show');
        // Ganti route 'scan' lama Anda dengan ini
        Route::get('/scan/{sesi_absensi}', [Operator\AbsensiController::class, 'scan'])->name('scan');

        Route::get('', [Operator\AbsensiController::class, 'export'])->name('export.absensi');
        // Tambahkan route baru ini untuk memproses data dari scanner
        Route::post('/scan/process', [Operator\AbsensiController::class, 'processScan'])->name('scan.process');
        // ✅ Menyimpan absensi manual (tanpa parameter di URL)
        Route::post('/manual', [Operator\AbsensiController::class, 'storeManual'])->name('manual');
        // ✅ Membatalkan absensi manual (menggunakan method DELETE yang lebih tepat)
        Route::delete('/cancel', [Operator\AbsensiController::class, 'cancelManual'])->name('cancel');
    });
        


});



Route::middleware(['auth', 'role:peserta'])->group(function () {
    
});
