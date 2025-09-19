<div align="center">

<h1 align="center">Sistem Absensi Berbasis QR Code</h1>

<p align="center">
Sebuah solusi modern untuk manajemen kehadiran acara yang efisien, dibangun dengan Laravel.
</p>

<!-- Badges -->

<p align="center">
<img src="https://www.google.com/search?q=https://img.shields.io/badge/Laravel-10-FF2D20%3Fstyle%3Dfor-the-badge%26logo%3Dlaravel" alt="Laravel 10">
<img src="https://www.google.com/search?q=https://img.shields.io/badge/PHP-8.3-777BB4%3Fstyle%3Dfor-the-badge%26logo%3Dphp" alt="PHP 8.3">
<img src="https://www.google.com/search?q=https://img.shields.io/badge/Bootstrap-5-7952B3%3Fstyle%3Dfor-the-badge%26logo%3Dbootstrap" alt="Bootstrap 5">
<img src="https://www.google.com/search?q=https://img.shields.io/badge/License-MIT-yellow.svg%3Fstyle%3Dfor-the-badge" alt="License: MIT">
</p>
</div>
🎯 Latar Belakang

Manajemen kehadiran di acara seringkali diwarnai oleh antrian panjang dan rekapitulasi data manual yang memakan waktu. Aplikasi ini hadir sebagai solusi untuk mengotomatiskan seluruh proses, mulai dari pendaftaran peserta hingga pelaporan kehadiran, menggunakan teknologi QR Code yang cepat dan andal.
	
🚀 Fitur Utama

Aplikasi ini dirancang dengan tiga peran pengguna yang memiliki hak akses berbeda untuk memastikan alur kerja yang aman dan terorganisir.

<details>
<summary><strong>👤 Admin - Pusat Kontrol Platform</strong></summary>

    Manajemen Operator: CRUD (Create, Read, Update, Delete) untuk akun operator.

    Manajemen Akun Peserta: Mengelola semua akun pengguna dengan peran 'peserta'.

    Manajemen Kegiatan: Memantau dan mengelola semua kegiatan yang ada di platform.

</details>

<details>
<summary><strong>💼 Operator - Pengelola Acara</strong></summary>

    Manajemen Kegiatan & Sesi: Membuat kegiatan baru dan sesi absensi di dalamnya, lengkap dengan proteksi tabrakan jadwal.

    Manajemen Peserta:

        Menambah peserta secara manual.

        Mengimpor & Mengekspor data peserta via Excel (dengan fitur re-impor untuk update).

        Menghapus semua peserta dalam satu kegiatan.

    Pelacakan & Pelaporan:

        Dashboard detail per kegiatan dengan statistik kehadiran yang relevan.

        Mengekspor laporan absensi (log per sesi atau rekapitulasi kehadiran).

    Fungsionalitas QR Code:

        Scan absensi peserta menggunakan kamera.

        Menyediakan link "Scan Mandiri" yang bisa dibagikan.

</details>

<details>
<summary><strong>👨‍🎓 Peserta - Pengguna Akhir</strong></summary>

    Registrasi Aman: Halaman pendaftaran publik dilindungi oleh Honeypot dan Google reCAPTCHA v3.

    Dashboard Peserta: Melihat daftar kegiatan yang tersedia dan status pendaftaran.

    Pendaftaran Mudah: Mendaftar ke kegiatan melalui modal konfirmasi untuk melengkapi data profil.

    Manajemen QR Code: Melihat dan mengunduh QR Code unik untuk setiap kegiatan yang diikuti.

</details>
🔧 Teknologi & Paket Utama

    Backend: Laravel 10, PHP 8.3

    Frontend: Bootstrap 5, AdminLTE, JavaScript, jQuery

    Database: MySQL

    Paket Kunci:

        maatwebsite/excel: Untuk semua fungsionalitas import dan export data Excel.

        simplesoftwareio/simple-qrcode: Untuk men-generate semua QR Code.

        anhskohbo/no-captcha: Untuk integrasi Google reCAPTCHA v3.

⚙️ Panduan Instalasi Lokal

Untuk menjalankan proyek ini di lingkungan lokal, ikuti langkah-langkah berikut:

    Clone Repository

    git clone https://github.com/zahruddin/absensi-kegiatan-qrcode.git
    cd absensi-kegiatan-qrcode

    Instal Dependensi

    composer install
    npm install
    npm run build

    Siapkan File Environment (.env)
    Salin file .env.example, lalu sesuaikan konfigurasi database Anda.

    cp .env.example .env
    php artisan key:generate

    Jalankan Migrasi Database

    php artisan migrate

    Buat Symbolic Link
    Perintah ini penting agar file yang diunggah (seperti QR Code) bisa diakses dari web.

    php artisan storage:link

    Jalankan Server Development

    php artisan serve

    Aplikasi Anda sekarang bisa diakses di http://127.0.0.1:8000.

🚀 Alur Deployment ke Server (cPanel)

    Push ke GitHub: Pastikan semua perubahan sudah di-push ke repository Anda.

    Login ke Server: Gunakan SSH untuk masuk ke server hosting Anda.

    Tarik Perubahan: Masuk ke direktori proyek dan jalankan git pull origin main.

    Instal Dependensi:

    # Gunakan path PHP yang benar jika perlu
    /opt/cpanel/ea-php83/root/usr/bin/php /usr/local/bin/composer install --optimize-autoloader --no-dev

    Jalankan Migrasi:

    php artisan migrate --force

    Optimalkan Aplikasi: Bersihkan cache lama dan buat cache baru yang teroptimasi.

    php artisan optimize

📄 Lisensi

Proyek ini berada di bawah Lisensi MIT. Lihat file LICENSE untuk detail lebih lanjut.