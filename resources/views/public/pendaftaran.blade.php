<h1>Pendaftaran: {{ $kegiatan->nama }}</h1>
<p>Tanggal: {{ $kegiatan->date }}</p>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
@endif

@auth
    {{-- Jika pengguna sudah login --}}
    @if ($isRegistered)
        <p>Anda sudah terdaftar sebagai peserta. Terima kasih!</p>
    @else
        <p>Anda akan mendaftar sebagai: <strong>{{ Auth::user()->name }}</strong></p>
        <form action="{{ route('kegiatan.register.store', $kegiatan->id) }}" method="POST">
            @csrf
            <button type="submit">Konfirmasi Pendaftaran</button>
        </form>
    @endif
@else
    {{-- Jika pengguna belum login --}}
    <p>Silakan <a href="{{ route('login') }}">login</a> atau <a href="{{ route('register') }}">buat akun</a> untuk mendaftar sebagai peserta.</p>
@endauth