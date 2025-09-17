<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Operator;
use App\Http\Controllers\Peserta;
use App\Http\Controllers\Operator\SesiAbsensiController; 
use App\Http\Controllers\Public\PendaftaranController;
use App\Http\Controllers\Public;


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
Route::get('/register/ka', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register/ka', [RegisterController::class, 'register']);
Route::get('/register/{kegiatan}', [PendaftaranController::class, 'show'])->name('kegiatan.register.show');
Route::post('/register/{kegiatan}', [PendaftaranController::class, 'store'])->name('kegiatan.register.store')->middleware('auth');

Route::get('/kegiatan/info/{id}', [App\Http\Controllers\InfoController::class, 'show'])->name('kegiatan.info');

Route::post('/logout', function () {
    Auth::logout();  // Menjalankan proses logout
    return redirect()->route('login');  // Mengarahkan pengguna ke halaman login setelah logout
})->name('logout');



// ====== ROUTE UNTUK SCAN MANDIRI (PUBLIK) ======
// Halaman untuk menampilkan scanner
Route::get('/scan-mandiri/{kegiatan}', [Public\ScanController::class, 'show'])->name('scan.mandiri.show');
// Endpoint untuk memproses hasil scan dari halaman publik
Route::post('/scan-mandiri/process', [Public\ScanController::class, 'process'])->name('scan.mandiri.process');


Route::get('/scan/{idmeja}', [Customer\MenuController::class, 'scanQRCode'])->name('customer.scan.qrcode');
Route::post('/konfirmasi', [Customer\MenuController::class, 'konfirmasiPembayaran'])->name('customer.konfirmasi');

// ====== GRUP ROUTE ADMIN YANG DISESUAIKAN DENGAN ATURAN ANDA ======
Route::middleware(['auth', 'role:admin'])
     ->prefix('admin') // <- Menghindari pengetikan '/admin' berulang kali
     ->name('admin.')  // <- Menghindari pengetikan 'admin.' berulang kali
     ->group(function () {

    // --- Dashboard & Profile ---
    // URL: /admin/dashboard -> Nama Route: admin.dashboard
    // Redirect dari /admin ke /admin/dashboard
    Route::get('/', function() { return redirect()->route('admin.dashboard'); });
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // URL: /admin/profile -> Nama Route: admin.profile
    Route::get('/profile', [Admin\ProfileController::class, 'index'])->name('profile');


    // --- Kelola Operator ---
    Route::get('/operator', [Admin\OperatorController::class, 'index'])->name('operator.index');
    Route::post('/operator/store', [Admin\OperatorController::class, 'store'])->name('operator.store');
    Route::put('/operator/update/{user}', [Admin\OperatorController::class, 'update'])->name('operator.update');
    Route::delete('/operator/destroy/{user}', [Admin\OperatorController::class, 'destroy'])->name('operator.destroy');


    // --- Kelola Peserta ---
    // URL: /admin/peserta -> Nama Route: admin.peserta.index
    Route::get('/peserta', [Admin\PesertaController::class, 'index'])->name('peserta.index');
    Route::post('/store', [Admin\PesertaController::class, 'store'])->name('peserta.store');
    Route::put('/update/{user}', [Admin\PesertaController::class, 'update'])->name('peserta.update');
    Route::delete('/destroy/{user}', [Admin\PesertaController::class, 'destroy'])->name('peserta.destroy');
    // ... Tambahkan route store, update, destroy untuk peserta di sini jika Anda perlukan ...


    // --- Kelola Kegiatan ---
    // URL: /admin/kegiatan -> Nama Route: admin.kegiatan.index
    Route::get('/kegiatan', [Admin\KegiatanController::class, 'index'])->name('kegiatan.index');
    // ... Tambahkan route store, update, destroy untuk kegiatan di sini jika Anda perlukan ...
});

// operator
Route::middleware(['auth', 'role:operator'])->group(function () {

    // DASHBOARD
    Route::get('/operator', [Operator\DashboardController::class, 'index'])
        ->name('operator.dashboard');


    // ====== KELOLA KEGIATAN ======
    Route::prefix('operator/kegiatan')->name('operator.kegiatan.')->group(function () {
        Route::get('/', [Operator\KegiatanController::class, 'index'])->name('index');
        Route::get('/detail/{kegiatan}', [Operator\KegiatanController::class, 'detail'])->name('detail');
        Route::post('/store', [Operator\KegiatanController::class, 'store'])->name('store');
        Route::put('/update/{kegiatan}', [Operator\KegiatanController::class, 'update'])->name('update');
        Route::delete('/destroy/{kegiatan}', [Operator\KegiatanController::class, 'destroy'])->name('destroy');
        Route::get('/download-qrcode/{kegiatan}', [Operator\KegiatanController::class, 'downloadQRCode'])->name('download_qrcode');
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



    // ====== ROUTE UNTUK DASHBOARD PESERTA ======
Route::middleware(['auth', 'role:peserta'])->prefix('peserta')->name('peserta.')->group(function () {
    
    // Halaman utama dashboard
    Route::get('/dashboard', [Peserta\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/kegiatan/{kegiatan}/register', [Peserta\DashboardController::class, 'register'])->name('kegiatan.register');

});
