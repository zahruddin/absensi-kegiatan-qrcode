<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrackBooth | Login</title>
    <meta name="author" content="TrackBooth Team">
    <meta name="description" content="TrackBooth - Point of Sales Solution for Booth Businesses.">
    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
    />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
      integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg="
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
      integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI="
      crossorigin="anonymous"
    />


    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/custom.css') }}">
</head>
<body class="login-page bg-body-secondary">
    <div class="register-box">
        <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="#" class="h1"><b>REGISTRASI ABSENSI</b></a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Register akun baru</p>

            @include('components.alert')


            <form action="{{ url('/register') }}" method="POST">
            @csrf
            <div class="input-group mb-3">
                <input type="text" name="name" class="form-control" placeholder="Nama Lengkap" value="{{ old('name') }}">
                <div class="input-group-text"><span class="fas fa-user"></span></div>
            </div>

            <div class="input-group mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" value="{{ old('username') }}">
                <div class="input-group-text"><span class="fas fa-user-tag"></span></div>
            </div>

            <div class="input-group mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}">
                <div class="input-group-text"><span class="fas fa-envelope"></span></div>
            </div>

            <div class="input-group mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password">
                <div class="input-group-text"><span class="fas fa-lock"></span></div>
            </div>

            <div class="input-group mb-3">
                <input type="password" name="password_confirmation" class="form-control" placeholder="Konfirmasi Password">
                <div class="input-group-text"><span class="fas fa-lock"></span></div>
            </div>

            <div class="row">
                <div class="col-8">
                <a href="{{ url('/login') }}">Sudah punya akun?</a>
                </div>
                <div class="col-4">
                <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                </div>
            </div>
            </form>
        </div>
        </div>
    </div>

    <script src="{{ asset('dist/js/adminlte.js') }}"></script>
    <script src="{{ asset('dist/js/custom.js') }}"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('loginPassword');
            const icon = this;
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    </script>
</body>
</html>
