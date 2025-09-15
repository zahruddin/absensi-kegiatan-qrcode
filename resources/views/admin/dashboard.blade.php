@extends('layouts.app')

@section('title', 'Dashboard | Admin')
@section('page', 'Dashboard')

@section('content')
<div class="app-content">
    <div class="container-fluid">
        <h1 class="mb-4">Dashboard Admin</h1>

        <!-- KARTU STATISTIK UTAMA -->
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-white bg-primary h-100 shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Total Kegiatan</h5>
                                <h2>{{ $totalKegiatan }}</h2>
                            </div>
                            <i class="bi bi-calendar-event fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-white bg-info h-100 shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Total Operator</h5>
                                <h2>{{ $totalOperator }}</h2>
                            </div>
                            <i class="bi bi-person-badge fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-white bg-warning h-100 shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Total Peserta</h5>
                                <h2>{{ $totalPeserta }}</h2>
                            </div>
                            <i class="bi bi-people-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-white bg-success h-100 shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Kegiatan Aktif</h5>
                                <h2>{{ $kegiatanAktif }}</h2>
                            </div>
                            <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRAFIK & TABEL KEGIATAN TERBARU -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                       <i class="bi bi-bar-chart-line-fill"></i> Aktivitas Kegiatan (6 Bulan Terakhir)
                    </div>
                    <div class="card-body">
                        <div style="height: 300px;">
                            <canvas id="kegiatanChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 mt-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                       <i class="bi bi-list-stars"></i> Kegiatan Terbaru
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Nama Kegiatan</th>
                                        <th>Operator</th>
                                        <th class="text-center">Jumlah Peserta</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kegiatanTerbaru as $kegiatan)
                                        <tr>
                                            <td><strong>{{ $kegiatan->nama }}</strong></td>
                                            <td>{{ $kegiatan->user->name ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $kegiatan->peserta_count }}</td>
                                            <td>{{ \Carbon\Carbon::parse($kegiatan->date)->isoFormat('D MMM Y') }}</td>
                                            <td>
                                                @if($kegiatan->date >= now()->format('Y-m-d'))
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-dark">Selesai</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{-- <a href="{{ route('admin.kegiatan.show', $kegiatan->id) }}" class="btn btn-sm btn-outline-primary">Lihat Detail</a> --}}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Belum ada kegiatan yang dibuat.</td>
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
                    label: 'Jumlah Kegiatan',
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
                            stepSize: 1 // Hanya tampilkan angka bulat di sumbu Y
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
