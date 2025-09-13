@extends('layouts.app')

@section('title', 'Detail Sesi: ' . $sesi_absensi->nama)
@section('page', 'Detail Sesi Absensi')

@section('content')
<div class="container-fluid">
    
    {{-- Tombol Kembali dan Judul Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>{{ $sesi_absensi->nama }}</h3>
            <p class="text-muted mb-0">Kegiatan: {{ $kegiatan->nama }}</p>
        </div>
        <a href="{{ route('operator.kegiatan.detail', $kegiatan->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Detail Kegiatan
        </a>
    </div>

    {{-- STATISTIK SESI --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Peserta</h5>
                    <h2>{{ $statistik['totalPeserta'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Sudah Hadir</h5>
                    <h2 class="text-success">{{ $statistik['jumlahHadir'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Belum Hadir</h5>
                    <h2 class="text-danger">{{ $statistik['jumlahBelumHadir'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- DAFTAR PESERTA --}}
    <div class="row mb-4">
        {{-- Tabel Peserta Sudah Hadir --}}
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-check-circle-fill"></i> Peserta Sudah Hadir</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Peserta</th>
                                <th>NIM</th>
                                <th>kelompok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pesertaHadir as $index => $peserta)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $peserta->nama }}</td>
                                <td>{{ $peserta->nim ?? '-' }}</td>
                                <td>{{ $peserta->kelompok ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        {{-- Tabel Peserta Belum Hadir --}}
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-x-circle-fill"></i> Peserta Belum Hadir</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Peserta</th>
                                <th>NIM</th>
                                <th>Kelompok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pesertaBelumHadir as $index => $peserta)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $peserta->nama }}</td>
                                <td>{{ $peserta->nim ?? '-' }}</td>
                                <td>{{ $peserta->kelompok ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')

{{-- Inisialisasi DataTables untuk kedua tabel --}}
<script>
    $(document).ready(function() {
        $('.datatable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"
            },
            "responsive": true
        });
    });
</script>
@endsection

