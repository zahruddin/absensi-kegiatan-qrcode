<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Kasir;
use App\Http\Controllers\Customer;


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

Route::post('/logout', function () {
    Auth::logout();  // Menjalankan proses logout
    return redirect()->route('login');  // Mengarahkan pengguna ke halaman login setelah logout
})->name('logout');


Route::get('/scan/{idmeja}', [Customer\MenuController::class, 'scanQRCode'])->name('customer.scan.qrcode');
Route::post('/konfirmasi', [Customer\MenuController::class, 'konfirmasiPembayaran'])->name('customer.konfirmasi');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function() { return redirect()->route('admin.dashboard'); })->name('redirect.admin.dashboard'); 
    Route::get('/admin/dashboard', [Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/admin/laporanpenjualan', [Admin\LaporanPenjualanController::class, 'index'])->name('admin.laporanPenjualan');

    Route::get('/admin/profile', [Admin\ProfileController::class, 'index'])->name('admin.profile');

    Route::get('/admin/kelolauser', [Admin\kelolaUserController::class, 'index'])->name('admin.kelolaUsers');
    Route::post('/admin/kelolauser', [Admin\kelolaUserController::class, 'store'])->name('admin.kelolauser.add');
    Route::post('/admin/kelolauser/{id}', [Admin\kelolaUserController::class, 'delete'])->name('admin.kelolauser.delete');
    Route::put('/admin/kelolauser/update/{id}', [Admin\kelolaUserController::class, 'update'])->name('admin.kelolauser.update');

    Route::get('/admin/kelolaproduk', [Admin\ProdukController::class, 'index'])->name('admin.kelolaproduk');
    Route::post('/admin/kelolaproduk', [Admin\ProdukController::class, 'store'])->name('admin.kelolaproduk.add');
    Route::put('/admin/kelolaproduk/{id}', [Admin\ProdukController::class, 'update'])->name('admin.kelolaproduk.update');
    Route::post('/admin/kelolaproduk/{id}', [Admin\ProdukController::class, 'delete'])->name('admin.kelolaproduk.delete');

    Route::get('/admin/kelolameja', [Admin\KelolaMejaController::class, 'index'])->name('admin.kelolameja');
    Route::post('/admin/kelolameja', [Admin\KelolaMejaController::class, 'store'])->name('admin.kelolameja.add');
    Route::put('/admin/kelolameja/update/{id}', [Admin\KelolaMejaController::class, 'update'])->name('admin.kelolameja.update');
    Route::post('/admin/kelolameja/{id}', [Admin\KelolaMejaController::class, 'delete'])->name('admin.kelolameja.delete');

});

Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/kasir', function() { return redirect()->route('kasir.dashboard'); })->name('redirect.kasir.dashboard'); 
    Route::get('/kasir/dashboard', [Kasir\DashboardController::class, 'index'])->name('kasir.dashboard');
    Route::post('/kasir/dashboard/proses/{sale}', [Kasir\DashboardController::class, 'proses'])->name('kasir.proses');
    Route::get('/kasir/struk/{sale}', [Kasir\DashboardController::class, 'struk'])->name('kasir.struk');

    Route::get('/kasir/dashboard/data-penjualan', [Kasir\DashboardController::class, 'getSalesData']);


    Route::get('/kasir/datasales', [Kasir\DataSalesController::class, 'index'])->name('kasir.datasales');
    Route::get('/kasir/kelolaproduk', [Kasir\KelolaProdukController::class, 'index'])->name('kasir.kelolaproduk');
    Route::get('/kasir/sales', [Kasir\DashboardController::class, 'index'])->name('kasir.sales');
    Route::get('/kasir/profile', [Kasir\ProfileController::class, 'index'])->name('kasir.profile');
});

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/', function() { return redirect()->route('customer.menu'); })->name('redirect.customer.menu'); 
    Route::get('/menu', [Customer\MenuController::class, 'index'])->name('customer.menu');
    Route::get('/riwayat', [Customer\RiwayatController::class, 'index'])->name('customer.riwayat');

    Route::get('/keranjang', [Customer\KeranjangController::class, 'index'])->name('customer.keranjang');
    Route::get('/profile', [Customer\ProfileController::class, 'index'])->name('customer.profile');
});
