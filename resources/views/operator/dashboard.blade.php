@extends('layouts.app')

@section('title', 'Dashboard | Operator')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Dashboard Operator</h1>

    <!-- Statistik -->
    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Kegiatan</h5>
                    <h2>{{ $totalKegiatan }}</h2>
                </div>
            </div>
        </div>
        {{-- <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Peserta</h5>
                    <h2>{{ $totalPeserta }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Sudah Hadir</h5>
                    <h2>{{ $totalAbsensi }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Belum Hadir</h5>
                    <h2>{{ $belumHadir }}</h2>
                </div>
            </div>
        </div> --}}
    </div>

    <!-- Tabel kegiatan terbaru -->
    <div class="card mt-4">
        <div class="card-header">Kegiatan Terbaru</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nama Kegiatan</th>
                        <th>Tanggal</th>
                        {{-- <th>Lokasi</th> --}}
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kegiatanTerbaru as $kegiatan)
                        <tr>
                            <td>{{ $kegiatan->nama }}</td>
                            <td>{{ $kegiatan->date }}</td>
                            {{-- <td>{{ $kegiatan->lokasi }}</td> --}}
                            <td>
                                <a href="{{ route('operator.kegiatan.detail', $kegiatan->id) }}" class="btn btn-sm btn-primary">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada kegiatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
