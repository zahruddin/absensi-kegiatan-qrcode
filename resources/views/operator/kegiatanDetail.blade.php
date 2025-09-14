@extends('layouts.app')

{{-- Title dan Page menggunakan nama kegiatan --}}
@section('title', $kegiatan->nama . ' | Operator')
@section('page', $kegiatan->nama)

@section('content')

@push('style')
<style>
    .pulsing-badge {
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); }
        100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
    }
</style>
@endpush

<div class="app-content">
    <div class="container-fluid">

        {{-- ALERT UNTUK NOTIFIKASI --}}
        @include('components.alert')

        {{-- ====== STATISTIK KEHADIRAN (VERSI BARU YANG LEBIH RELEVAN) ====== --}}
        <div class="row mb-4">
            
            {{-- 1. Kehadiran Sesi Aktif --}}
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card text-center shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        @if ($sesiAktif)
                            <h5 class="card-title text-success">
                                <i class="bi bi-broadcast"></i> Sesi Aktif Saat Ini
                            </h5>
                            {{-- Menampilkan jumlah hadir di sesi yang sedang berjalan --}}
                            <h2>{{ $sesiAktif->absensi_count }} <small class="fs-5 text-muted">/ {{ $kegiatan->peserta_count }}</small></h2>
                            <p class="mb-0 text-muted small">{{ $sesiAktif->nama }}</p>
                        @else
                            <h5 class="card-title">Sesi Aktif</h5>
                            <div class="d-flex align-items-center justify-content-center h-100">
                                <h4 class="text-muted my-2">Tidak Ada</h4>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 2. Tingkat Partisipasi Peserta --}}
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card text-center shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5 class="card-title">Tingkat Partisipasi</h5>
                        <h2>{{ $tingkatPartisipasi }}<small class="fs-4">%</small></h2>
                        <p class="text-muted mb-0 small">Peserta yang hadir min. 1x</p>
                    </div>
                </div>
            </div>

            {{-- 3. Tingkat Kehadiran Total per Sesi --}}
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card text-center shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5 class="card-title">Kehadiran / Sesi</h5>
                        <h2>{{ $tingkatKehadiranTotal }}<small class="fs-4">%</small></h2>
                        <div class="progress mt-1" style="height: 10px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $tingkatKehadiranTotal }}%;" ></div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- 4. Total Peserta Terdaftar --}}
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card text-center shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5 class="card-title">Total Peserta</h5>
                        <h2>{{ $kegiatan->peserta_count }}</h2>
                        <p class="text-muted mb-0 small">Terdaftar</p>
                    </div>
                </div>
            </div>

            {{-- 5. Progres Sesi Berjalan --}}
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card text-center shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5 class="card-title">Progres Sesi</h5>
                        <h2>{{ $sesiSelesai }} <small class="fs-5 text-muted">/ {{ $sesiAbsensi->count() }}</small></h2>
                        <p class="text-muted mb-0 small">Sesi Selesai</p>
                    </div>
                </div>
            </div>

        </div>




        {{-- ====== DAFTAR SESI ABSENSI ====== --}}
        {{-- ====== DAFTAR SESI ABSENSI ====== --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Sesi Absensi</h5>
                <div>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exportAbsensiModal">
                        <i class="bi bi-file-earmark-excel"></i> Export Absensi
                    </button>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahSesiModal">
                        <i class="bi bi-plus-circle"></i> Tambah Sesi
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="sesiTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Sesi</th>
                            {{-- ✅ DITAMBAHKAN: Kolom Status --}}
                            <th>Status</th>
                            <th>Waktu Mulai</th>
                            {{-- ✅ DITAMBAHKAN: Header untuk Waktu Selesai --}}
                            <th>Waktu Selesai</th>
                            <th>Jumlah Hadir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sesiAbsensi as $index => $sesi)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $sesi->nama }}</td>
                                
                                {{-- ✅ DITAMBAHKAN: Logika untuk menampilkan status sesi secara dinamis --}}
                                <td>
                                    @if ($sesi->waktu_mulai && $sesi->waktu_selesai)
                                        @if(now()->between($sesi->waktu_mulai, $sesi->waktu_selesai))
                                            <span class="badge bg-success pulsing-badge">Sedang Berlangsung</span>
                                        @elseif(now()->isBefore($sesi->waktu_mulai))
                                            <span class="badge bg-secondary">Akan Datang</span>
                                        @else
                                            <span class="badge bg-dark">Selesai</span>
                                        @endif
                                    @else
                                        <span class="badge bg-warning">Jadwal Belum Diatur</span>
                                    @endif
                                </td>

                                <td>
                                    @if($sesi->waktu_mulai)
                                        {{ $sesi->waktu_mulai->format('d M Y, H:i') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($sesi->waktu_selesai)
                                        {{ $sesi->waktu_selesai->format('d M Y, H:i') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                <td>{{ $sesi->absensi_count }}</td>
                                
                                <td>
                                    <a href="{{ route('operator.absensi.show', $sesi->id) }}" class="btn btn-info btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    {{-- ✅ DITAMBAHKAN: Tombol Scan QR hanya muncul jika sesi sedang berlangsung --}}
                                    @if ($sesi->waktu_mulai && $sesi->waktu_selesai && now()->between($sesi->waktu_mulai, $sesi->waktu_selesai))
                                        <a href="{{ route('operator.absensi.scan', $sesi->id) }}" class="btn btn-success btn-sm" target="_blank" title="Scan QR">
                                            <i class="bi bi-qr-code-scan"></i>
                                        </a>
                                    @endif
                                    
                                    <button class="btn btn-warning btn-sm btn-edit-sesi"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editSesiModal"
                                            data-id="{{ $sesi->id }}"
                                            data-nama="{{ $sesi->nama }}"
                                            {{-- ✅ DIPERBAIKI: Mencegah error jika waktu null --}}
                                            data-waktu_mulai="{{ $sesi->waktu_mulai ? $sesi->waktu_mulai->format('Y-m-d H:i') : '' }}"
                                            data-waktu_selesai="{{ $sesi->waktu_selesai ? $sesi->waktu_selesai->format('Y-m-d H:i') : '' }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button class="btn btn-danger btn-sm btn-hapus-sesi"
                                            data-bs-toggle="modal"
                                            data-bs-target="#hapusSesiModal"
                                            data-id="{{ $sesi->id }}"
                                            data-nama="{{ $sesi->nama }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Untuk membuat efek kedip pada badge "Sedang Berlangsung" --}}

        {{-- ====== DATA PESERTA ====== --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Data Peserta</h5>
                <div>
                    <a href="{{ route('operator.peserta.export.linkqr', $kegiatan->id) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-file-earmark-excel"></i> Export link qrcode
                    </a>
                    <a href="{{ route('operator.peserta.export', $kegiatan->id) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </a>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#importPesertaModal">
                        <i class="bi bi-upload"></i> Import Peserta
                    </button>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#tambahPesertaModal">
                        <i class="bi bi-plus-circle"></i> Tambah Peserta
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="pesertaTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Peserta</th>
                            <th>Kelompok</th>
                            {{-- Tampilkan nama sesi sebagai header kolom status --}}
                            @foreach ($sesiAbsensi as $sesi)
                                <th class="text-center">{{ $sesi->nama }}</th>
                            @endforeach
                            <th>QR Code</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Loop peserta yang sudah di-eager load --}}
                        @foreach ($kegiatan->peserta as $index => $peserta)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $peserta->nama }}</td>
                                <td>{{ $peserta->kelompok }}</td>
                                
                                {{-- Cek kehadiran untuk setiap sesi dengan lookup cepat --}}
                                @foreach($sesiAbsensi as $sesi)
                                    <td class="text-center">
                                        @if(isset($kehadiranPeserta[$peserta->id]) && $kehadiranPeserta[$peserta->id]->contains($sesi->id))
                                            <i class="bi bi-check-circle-fill text-success" title="Hadir"></i>
                                        @else
                                            <i class="bi bi-x-circle-fill text-danger" title="Tidak Hadir"></i>
                                        @endif
                                    </td>
                                @endforeach
                                <td>
                                    @if($peserta->qrcode)
                                        <img src="{{ asset('storage/'.$peserta->qrcode) }}" alt="QR Code {{ $peserta->nama }}" width="50">
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Tombol Absen Manual --}}
                                    <button class="btn btn-success btn-sm btn-absen-manual" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#absenManualModal"
                                            data-id-peserta="{{ $peserta->id }}"
                                            data-nama-peserta="{{ $peserta->nama }}">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                    
                                    {{-- ✅ TOMBOL EDIT PESERTA (BARU) --}}
                                    <button class="btn btn-warning btn-sm btn-edit-peserta"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editPesertaModal"
                                            data-id="{{ $peserta->id }}"
                                            data-nama="{{ $peserta->nama }}"
                                            data-email="{{ $peserta->email }}"
                                            data-no_hp="{{ $peserta->no_hp }}"
                                            data-prodi="{{ $peserta->prodi }}"
                                            data-nim="{{ $peserta->nim }}"
                                            data-kelompok="{{ $peserta->kelompok }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    {{-- Tombol Hapus Peserta --}}
                                    <button class="btn btn-danger btn-sm btn-hapus-peserta"
                                            data-bs-toggle="modal"
                                            data-bs-target="#hapusPesertaModal"
                                            data-id="{{ $peserta->id }}"
                                            data-nama="{{ $peserta->nama }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    
                                    {{-- Tombol Download QR Code --}}
                                    <a href="{{ route('operator.peserta.download_qrcode', $peserta->id) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


{{-- MODAL CARD SESI KEGIATAN --}}
<!-- Modal Tambah Sesi -->
<div class="modal fade" id="tambahSesiModal" tabindex="-1" aria-labelledby="tambahSesiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('operator.kegiatan.sesi.store', $kegiatan->id) }}" method="POST">
                @csrf
                {{-- ✅ Tambahkan penanda ini --}}
                <input type="hidden" name="form_type" value="add_sesi">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahSesiModalLabel">Tambah Sesi Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Sesi</label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required>
                            @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="jam_mulai" class="form-label">Jam Mulai</label>
                            <input type="time" class="form-control @error('jam_mulai') is-invalid @enderror" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai') }}" required>
                            @error('jam_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required>
                             @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="jam_selesai" class="form-label">Jam Selesai</label>
                            <input type="time" class="form-control @error('jam_selesai') is-invalid @enderror" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai') }}" required>
                             @error('jam_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- edit sesi --}}
<!-- Modal Edit Sesi -->
<div class="modal fade" id="editSesiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formEditSesi" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Sesi Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    
                    {{-- Input Nama Sesi --}}
                    <div class="mb-3">
                        <label for="edit_nama_sesi" class="form-label">Nama Sesi</label>
                        {{-- ✅ DITAMBAHKAN: class @error dan value old() --}}
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="edit_nama_sesi" name="nama" value="{{ old('nama') }}" required>
                        {{-- ✅ DITAMBAHKAN: Blok untuk menampilkan pesan error --}}
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Waktu Mulai (Dipisah) --}}
                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label for="edit_tanggal_mulai" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" id="edit_tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required>
                            @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="edit_jam_mulai" class="form-label">Jam Mulai</label>
                            <input type="time" class="form-control @error('jam_mulai') is-invalid @enderror" id="edit_jam_mulai" name="jam_mulai" value="{{ old('jam_mulai') }}" required>
                            @error('jam_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Waktu Selesai (Dipisah) --}}
                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label for="edit_tanggal_selesai" class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" id="edit_tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required>
                            @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="edit_jam_selesai" class="form-label">Jam Selesai</label>
                            <input type="time" class="form-control @error('jam_selesai') is-invalid @enderror" id="edit_jam_selesai" name="jam_selesai" value="{{ old('jam_selesai') }}" required>
                             @error('jam_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
{{-- hapus sesi --}}
<div class="modal fade" id="hapusSesiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formHapusSesi" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Hapus Sesi Absensi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus sesi <strong id="hapusSesiNama"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Modal Export Absensi -->
<div class="modal fade" id="exportAbsensiModal" tabindex="-1" aria-labelledby="exportAbsensiLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportAbsensiLabel">Export Data Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p>Pilih sesi absensi yang ingin Anda ekspor.</p>
                <div class="mb-3">
                    <label for="sesi_pilihan_export" class="form-label">Sesi Absensi</label>
                    <select id="sesi_pilihan_export" class="form-select">
                        {{-- Opsi untuk mengekspor semua sesi --}}
                        <option value="all">-- Semua Sesi --</option>
                        
                        {{-- Loop untuk setiap sesi yang tersedia --}}
                        @foreach ($sesiAbsensi as $sesi)
                            <option value="{{ $sesi->id }}">{{ $sesi->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                {{-- Tombol ini akan kita kontrol dengan JavaScript --}}
                <button type="button" class="btn btn-success" id="btn-do-export">
                    <i class="bi bi-download"></i> Export Sekarang
                </button>
            </div>
        </div>
    </div>
</div>


{{-- MODAL CARD PESERTA --}}
{{-- ================= MODAL IMPORT ================= --}}
<div class="modal fade" id="importPesertaModal" tabindex="-1" aria-labelledby="importPesertaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('operator.peserta.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_kegiatan" value="{{ $kegiatan->id }}">
            <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="importPesertaLabel"><i class="bi bi-upload"></i> Import Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p>Silakan unggah file Excel peserta sesuai format template yang sudah disediakan.</p>
                
                <div class="mb-3">
                    <label for="file_excel" class="form-label">Pilih File Excel</label>
                    <input type="file" name="file" id="file_excel" class="form-control" accept=".xls,.xlsx" required>
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Pastikan format file sesuai dengan template.
                </div>
            </div>
            <div class="modal-footer">
                {{-- Tombol Download Template --}}
                <a href="{{ asset('template/peserta_template.xlsx') }}" class="btn btn-outline-info">
                    <i class="bi bi-download"></i> Download Template
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Import</button>
            </div>
            </div>
        </form>
    </div>
</div>
<!-- Modal Tambah Peserta -->
<div class="modal fade" id="tambahPesertaModal" tabindex="-1" aria-labelledby="tambahPesertaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('operator.peserta.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_kegiatan" value="{{ $kegiatan->id }}">
                
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="tambahPesertaModalLabel">
                        <i class="bi bi-plus-circle"></i> Tambah Peserta
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{-- Nama --}}
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Peserta</label>
                        <input type="text" name="nama" id="nama" class="form-control" placeholder="Masukkan nama peserta" required>
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Peserta</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Masukkan email peserta" >
                    </div>

                    {{-- Nomor HP --}}
                    <div class="mb-3">
                        <label for="no_hp" class="form-label">No. HP</label>
                        <input type="text" name="no_hp" id="no_hp" class="form-control" placeholder="Masukkan nomor HP" >
                    </div>

                    {{-- Program Studi --}}
                    <div class="mb-3">
                        <label for="prodi" class="form-label">Program Studi</label>
                        <input type="text" name="prodi" id="prodi" class="form-control" placeholder="Masukkan program studi" >
                    </div>

                    {{-- NIM --}}
                    <div class="mb-3">
                        <label for="nim" class="form-label">NIM</label>
                        <input type="text" name="nim" id="nim" class="form-control" placeholder="Masukkan NIM" >
                    </div>

                    {{-- Kelompok --}}
                    <div class="mb-3">
                        <label for="kelompok" class="form-label">Kelompok</label>
                        <input type="text" name="kelompok" id="kelompok" class="form-control" placeholder="Masukkan kelompok">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Absen Manual --}}
<div class="modal fade" id="absenManualModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        {{-- Form action akan diisi oleh JavaScript --}}
        <form id="formAbsenManual" method="POST" action="#"> 
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Absensi Manual - <span id="pesertaNama">Nama Peserta</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_peserta" id="idPeserta">
                    <div class="mb-3">
                        <label for="id_sesi" class="form-label">Pilih Sesi Absensi</label>
                        {{-- NAME INPUT DIPERBARUI menjadi id_sesi --}}
                        <select name="id_sesi" id="id_sesi" class="form-select" required>
                            <option value="">-- Pilih Sesi --</option>
                            {{-- NAMA VARIABEL DIPERBARUI menjadi $sesiAbsensi --}}
                            @foreach($sesiAbsensi as $sesi)
                                <option value="{{ $sesi->id }}">{{ $sesi->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <div>
                        {{-- Tombol Batalkan Absen --}}
                        <button type="submit" class="btn btn-warning" id="btnCancelAbsen">
                            <i class="bi bi-x-circle"></i> Batalkan Absensi
                        </button>
                        {{-- Tombol Simpan Absen --}}
                        <button type="submit" class="btn btn-success" id="btnSaveAbsen">
                            <i class="bi bi-check-circle"></i> Simpan Absensi
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- MODAL Edit peserta --}}
<div class="modal fade" id="editPesertaModal" tabindex="-1" aria-labelledby="editPesertaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {{-- Action form akan diatur oleh JavaScript --}}
            <form id="formEditPeserta" method="POST" action="#">
                @csrf
                @method('PUT') {{-- <-- Penting untuk method update --}}
                <div class="modal-header">
                    <h5 class="modal-title" id="editPesertaModalLabel">Edit Data Peserta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="edit_nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nim" class="form-label">NIM</label>
                        <input type="text" class="form-control" id="edit_nim" name="nim" >
                    </div>
                     <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email">
                    </div>
                     <div class="mb-3">
                        <label for="edit_no_hp" class="form-label">No. HP</label>
                        <input type="text" class="form-control" id="edit_no_hp" name="no_hp">
                    </div>
                     <div class="mb-3">
                        <label for="edit_prodi" class="form-label">Program Studi</label>
                        <input type="text" class="form-control" id="edit_prodi" name="prodi">
                    </div>
                    <div class="mb-3">
                        <label for="edit_kelompok" class="form-label">Kelompok</label>
                        <input type="text" class="form-control" id="edit_kelompok" name="kelompok">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus Peserta --}}
<div class="modal fade" id="hapusPesertaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formHapusPeserta" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Konfirmasi Hapus Peserta</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus peserta <strong id="hapusPesertaNama"></strong> ?</p>
                    <p class="text-danger"><i class="bi bi-exclamation-triangle"></i> Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
{{-- datatables --}}
<script>
    $(document).ready(function() {
        $('#sesiTable, #pesertaTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "language": {
                "paginate": {
                    "first": "Awal",
                    "last": "Akhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                },
                "search": "Cari:",
                "info": "Menampilkan _START_ hingga _END_ dari _TOTAL_ data",
                "infoEmpty": "Tidak ada data",
                "lengthMenu": "Tampilkan _MENU_ per halaman"
            }
        });
    });
</script>
{{-- idkegiatan --}}
<script>
    const idKegiatan = {{ $kegiatan->id }};
</script>
{{-- Card sesi kegiatan --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {

        // ✅ LANGKAH 1: Buat fungsi yang bisa dipakai ulang untuk mengisi modal edit
        function populateEditSesiModal(button) {
            const idSesi = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const waktuMulai = button.getAttribute('data-waktu_mulai');
            const waktuSelesai = button.getAttribute('data-waktu_selesai');
            
            const editSesiModal = document.getElementById('editSesiModal');
            const form = editSesiModal.querySelector('#formEditSesi');

            // Buat URL action yang benar
            let actionUrl = "{{ route('operator.kegiatan.sesi.update', ['kegiatan' => $kegiatan->id, 'sesi_absensi' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', idSesi);
            form.setAttribute('action', actionUrl);

            // Isi semua input di dalam form
            form.querySelector('#edit_nama_sesi').value = nama;
            form.querySelector('#edit_tanggal_mulai').value = waktuMulai.substring(0, 10);
            form.querySelector('#edit_jam_mulai').value = waktuMulai.substring(11, 16);
            form.querySelector('#edit_tanggal_selesai').value = waktuSelesai.substring(0, 10);
            form.querySelector('#edit_jam_selesai').value = waktuSelesai.substring(11, 16);
        }

        // ===========================================
        // LOGIKA UNTUK MODAL EDIT SESI
        // ===========================================
        const editSesiModal = document.getElementById('editSesiModal');
        if (editSesiModal) {
            // Panggil fungsi di atas saat modal dibuka lewat tombol
            editSesiModal.addEventListener('show.bs.modal', function (event) {
                populateEditSesiModal(event.relatedTarget);
            });
        }

        // ===========================================
        // LOGIKA UNTUK MODAL HAPUS SESI
        // ===========================================
        const hapusSesiModal = document.getElementById('hapusSesiModal');
        if (hapusSesiModal) {
            hapusSesiModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idSesi = button.getAttribute('data-id');
                const namaSesi = button.getAttribute('data-nama');

                hapusSesiModal.querySelector('#hapusSesiNama').textContent = namaSesi;
                const formHapus = hapusSesiModal.querySelector('#formHapusSesi');

                let actionUrl = "{{ route('operator.kegiatan.sesi.destroy', ['kegiatan' => $kegiatan->id, 'sesi_absensi' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', idSesi);
                formHapus.setAttribute('action', actionUrl);
            });
        }

        // ===========================================
        // SCRIPT UNTUK MEMBUKA KEMBALI MODAL SETELAH VALIDASI GAGAL
        // ===========================================
        @if ($errors->any())
            const formType = "{{ old('form_type') }}";
            
            if (formType === 'add_sesi') {
                const tambahModal = new bootstrap.Modal(document.getElementById('tambahSesiModal'));
                tambahModal.show();
            } else if ("{{ session('failed_edit_sesi_id') }}") {
                // ✅ LANGKAH 2: Logika baru saat validasi edit gagal
                const failedId = "{{ session('failed_edit_sesi_id') }}";
                const editButton = document.querySelector(`.btn-edit-sesi[data-id="${failedId}"]`);
                
                if (editButton) {
                    // Panggil fungsi yang sama untuk mengisi form SEBELUM modal ditampilkan
                    populateEditSesiModal(editButton);
                    
                    // Baru tampilkan modalnya
                    const editModalInstance = new bootstrap.Modal(editSesiModal);
                    editModalInstance.show();
                }
            }
        @endif

        // ===========================================
        // LOGIKA UNTUK MODAL EXPORT ABSENSI
        // ===========================================
        const exportButton = document.getElementById('btn-do-export');
        if (exportButton) {
            exportButton.addEventListener('click', function() {
                const selectElement = document.getElementById('sesi_pilihan_export');
                const selectedSesiId = selectElement.value;
                if (!selectedSesiId) {
                    alert('Silakan pilih sesi terlebih dahulu.');
                    return;
                }
                let baseUrl = "{{ route('operator.absensi.export', [$kegiatan->id, 'PLACEHOLDER']) }}";
                let finalUrl = baseUrl.replace('PLACEHOLDER', selectedSesiId);
                window.location.href = finalUrl;
            });
        }

    });
</script>

{{-- Card Peserta --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {

        // ===========================================
        // untuk import export
        const exportButton = document.getElementById('btn-do-export');
        
        if (exportButton) {
            exportButton.addEventListener('click', function() {
                const selectElement = document.getElementById('sesi_pilihan_export');
                const selectedSesiId = selectElement.value;

                if (!selectedSesiId) {
                    alert('Silakan pilih sesi terlebih dahulu.');
                    return;
                }

                // Buat URL dasar dari route, gunakan placeholder
                // JavaScript disesuaikan dengan nama route baru
                let baseUrl = "{{ route('operator.absensi.export', [$kegiatan->id, 'PLACEHOLDER']) }}";
                
                // Ganti placeholder dengan ID sesi yang dipilih (bisa "all" atau angka)
                let finalUrl = baseUrl.replace('PLACEHOLDER', selectedSesiId);
                
                // Arahkan browser untuk men-download dari URL yang sudah jadi
                window.location.href = finalUrl;
            });
        }

        // ===========================================
        // LOGIKA UNTUK MODAL ABSEN MANUAL
        // ===========================================
        const absenManualModal = document.getElementById('absenManualModal');
        if (absenManualModal) {
            absenManualModal.addEventListener('show.bs.modal', function (event) {
                // ... (kode untuk mengambil dan mengisi data id_peserta & nama_peserta tetap sama)
                const button = event.relatedTarget;
                const idPeserta = button.getAttribute('data-id-peserta');
                const namaPeserta = button.getAttribute('data-nama-peserta');
                absenManualModal.querySelector('#pesertaNama').textContent = namaPeserta;
                absenManualModal.querySelector('#idPeserta').value = idPeserta;
                
                const form = absenManualModal.querySelector('#formAbsenManual');
                const btnSave = absenManualModal.querySelector('#btnSaveAbsen');
                const btnCancel = absenManualModal.querySelector('#btnCancelAbsen');
                
                // Hapus input _method jika ada dari klik sebelumnya
                const existingMethodInput = form.querySelector('input[name="_method"]');
                if (existingMethodInput) {
                    existingMethodInput.remove();
                }

                // Atur action untuk tombol Simpan (POST)
                btnSave.onclick = function() {
                    form.setAttribute('action', "{{ route('operator.absensi.manual') }}");
                };

                // Atur action untuk tombol Batalkan (DELETE)
                btnCancel.onclick = function() {
                    form.setAttribute('action', "{{ route('operator.absensi.cancel') }}");
                    // Tambahkan input _method untuk spoofing DELETE
                    if (!form.querySelector('input[name="_method"]')) {
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);
                    }
                };
            });
        }
       
        // ===========================================
        // LOGIKA UNTUK MODAL EDIT PESERTA
        // ===========================================
        const editPesertaModal = document.getElementById('editPesertaModal');
        if (editPesertaModal) {
            editPesertaModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const form = editPesertaModal.querySelector('#formEditPeserta');

                // ✅ Gunakan placeholder unik
                let actionUrl = "{{ route('operator.peserta.update', 'PLACEHOLDER') }}".replace('PLACEHOLDER', id);
                form.setAttribute('action', actionUrl);

                // Isi nilai ke setiap input di form
                form.querySelector('#edit_nama').value = button.getAttribute('data-nama');
                form.querySelector('#edit_email').value = button.getAttribute('data-email');
                form.querySelector('#edit_no_hp').value = button.getAttribute('data-no_hp');
                form.querySelector('#edit_prodi').value = button.getAttribute('data-prodi');
                form.querySelector('#edit_nim').value = button.getAttribute('data-nim');
                form.querySelector('#edit_kelompok').value = button.getAttribute('data-kelompok');
            });
        }

        // ===========================================
        // LOGIKA UNTUK MODAL HAPUS PESERTA
        // ===========================================
        const hapusPesertaModal = document.getElementById('hapusPesertaModal');
        if(hapusPesertaModal) {
            hapusPesertaModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');
                const formHapus = hapusPesertaModal.querySelector('#formHapusPeserta');

                // Tampilkan nama peserta yang akan dihapus
                const namaPesertaSpan = hapusPesertaModal.querySelector('#hapusPesertaNama');
                if(namaPesertaSpan) {
                    namaPesertaSpan.textContent = nama;
                }

                // ✅ Gunakan placeholder unik
                let actionUrl = "{{ route('operator.peserta.delete', 'PLACEHOLDER') }}".replace('PLACEHOLDER', id);
                if(formHapus) {
                    formHapus.setAttribute("action", actionUrl);
                }
            });
        }
    });
</script>
@endsection
