<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Absensi</title>
    <!-- Bootstrap Icons (Sangat direkomendasikan untuk ikon) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.css') }}">
    <style>
        /* Menambahkan cursor pointer untuk ikon mata */
        .password-toggle-icon {
            cursor: pointer;
        }
    </style>
    {{-- ✅ DITAMBAHKAN: Script reCAPTCHA dari Google --}}
    {!! NoCaptcha::renderJs() !!}
</head>
<body class="register-page bg-body-secondary">
    <div class="register-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="#" class="h1"><b>Registrasi</b> Absensi</a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Daftar untuk membuat akun baru</p>

                @include('components.alert')

                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    {{-- NAMA LENGKAP --}}
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama Lengkap" value="{{ old('name') }}" required>
                            <div class="input-group-text"><i class="bi bi-person"></i></div>
                        </div>
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- USERNAME --}}
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" placeholder="Username" value="{{ old('username') }}" required>
                            <div class="input-group-text"><i class="bi bi-at"></i></div>
                        </div>
                         @error('username')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- EMAIL --}}
                    <div class="mb-3">
                         <div class="input-group">
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required>
                            <div class="input-group-text"><i class="bi bi-envelope"></i></div>
                        </div>
                        @error('email')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- PASSWORD --}}
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
                            <div class="input-group-text">
                                <i class="bi bi-eye-slash password-toggle-icon" id="togglePassword"></i>
                            </div>
                        </div>
                        @error('password')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- KONFIRMASI PASSWORD --}}
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Konfirmasi Password" required>
                            <div class="input-group-text">
                                <i class="bi bi-eye-slash password-toggle-icon" id="togglePasswordConfirmation"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Field Jebakan Honeypot --}}
                    <div class="d-none" aria-hidden="true">
                        <label for="fax_number">Jangan isi field ini</label>
                        <input type="text" name="fax_number" id="fax_number" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="row">
                        <div class="col-8 d-flex align-items-center">
                            <a href="{{ route('login') }}">Saya sudah punya akun</a>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk menangani show/hide password
            function setupPasswordToggle(inputId, toggleIconId) {
                const passwordField = document.getElementById(inputId);
                const toggleIcon = document.getElementById(toggleIconId);

                if (passwordField && toggleIcon) {
                    toggleIcon.addEventListener('click', function() {
                        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordField.setAttribute('type', type);
                        this.classList.toggle('bi-eye');
                        this.classList.toggle('bi-eye-slash');
                    });
                }
            }
            setupPasswordToggle('password', 'togglePassword');
            setupPasswordToggle('password_confirmation', 'togglePasswordConfirmation');
        });
    </script>
    
    {{-- ✅ DITAMBAHKAN: Skrip untuk menjalankan reCAPTCHA v3 --}}
    <script>
    grecaptcha.ready(function() {
        grecaptcha.execute('{{ config('no-captcha.sitekey') }}', {action: 'register'}).then(function(token) {
            // Tambahkan input tersembunyi dengan token ke form
            let form = document.querySelector('form');
            if (form) {
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'g-recaptcha-response';
                input.value = token;
                form.appendChild(input);
            }
        });
    });
    </script>
</body>
</html>

