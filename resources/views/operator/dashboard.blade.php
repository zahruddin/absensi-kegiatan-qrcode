@extends('layouts.app')

@section('title', 'Dashboard | Operator')
@section('page', 'Dashboard')

@section('content')
<div class="app-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Total Kegiatan</h5>
                                <h2>{{ $totalKegiatan }}</h2>
                            </div>
                            <i class="bi bi-calendar-event fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-white bg-info h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Akan Datang</h5>
                                <h2>{{ $kegiatanAkanDatang }}</h2>
                            </div>
                            <i class="bi bi-clock-history fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-white bg-success h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Hari Ini</h5>
                                <h2>{{ $kegiatanHariIni }}</h2>
                            </div>
                            <i class="bi bi-calendar-check fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-white bg-warning h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Total Peserta</h5>
                                <h2>{{ $totalPeserta }}</h2>
                            </div>
                            <i class="bi bi-people-fill fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        Aktivitas Kegiatan (6 Bulan Terakhir)
                    </div>
                    <div class="card-body">
                        <canvas id="kegiatanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header">
                        <i class="bi bi-calendar-event"></i> 5 Kegiatan Mendatang
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <tbody>
                                @forelse($kegiatanMendatang as $kegiatan)
                                    <tr>
                                        <td>
                                            <a href="{{ route('operator.kegiatan.detail', $kegiatan->id) }}" class="fw-bold">{{ $kegiatan->nama }}</a><br>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($kegiatan->date)->isoFormat('dddd, D MMMM Y') }}</small>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('operator.kegiatan.detail', $kegiatan->id) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center">Tidak ada kegiatan yang akan datang.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header">
                        <i class="bi bi-check2-circle"></i> 5 Kegiatan Baru Selesai
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <tbody>
                                @forelse($kegiatanSelesai as $kegiatan)
                                     <tr>
                                        <td>
                                            <a href="{{ route('operator.kegiatan.detail', $kegiatan->id) }}" class="fw-bold">{{ $kegiatan->nama }}</a><br>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($kegiatan->date)->isoFormat('dddd, D MMMM Y') }}</small>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('operator.kegiatan.detail', $kegiatan->id) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center">Belum ada kegiatan yang selesai.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Library Chart.js untuk membuat grafik --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('kegiatanChart');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: '# Jumlah Kegiatan',
                    data: @json($chartData),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endpush