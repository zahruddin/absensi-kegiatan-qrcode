@extends('layouts.app')

{{-- Title dan Page menggunakan nama kegiatan --}}
@section('title', $kegiatan->nama . ' | Operator')
@section('page', $kegiatan->nama)

@section('content')
<div class="app-content">
    <div class="container-fluid">

        {{-- ALERT UNTUK NOTIFIKASI --}}
        @include('components.alert')

        {{-- ====== STATISTIK KEHADIRAN ====== --}}
        <div class="row mb-4">
            {{-- Jumlah Peserta --}}
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Jumlah Peserta</h5>
                        <h2>{{ $kegiatan->peserta()->count() ?? 0 }}</h2>
                    </div>
                </div>
            </div>

            {{-- Sudah Hadir --}}
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Sudah Hadir</h5>
                        <h2 class="text-success">{{ $absensi->where('status', 'hadir')->count() ?? 0 }}</h2>
                    </div>
                </div>
            </div>

            {{-- Belum Hadir --}}
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Belum Hadir</h5>
                        <h2 class="text-danger">
                            {{ ($kegiatan->peserta()->count() ?? 0) - ($absensi->where('status', 'hadir')->count() ?? 0) }}
                        </h2>
                    </div>
                </div>
            </div>

            {{-- Total Sesi Absensi --}}
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Total Sesi Absensi</h5>
                        <h2>{{ $kategoriAbsensi->count() }}</h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== DAFTAR SESI ABSENSI ====== --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Sesi Absensi</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahSesiModal">
                    <i class="bi bi-plus-circle"></i> Tambah Sesi
                </button>
            </div>
            <div class="card-body">
                <table id="sesiTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Sesi</th>
                            <th>Tanggal</th>
                            <th>Jumlah Hadir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kategoriAbsensi as $index => $kategori)
                            <tr>
                                <td>{{ $index+1 }}</td>
                                <td>{{ $kategori->nama }}</td>
                                <td>{{ $kategori->tanggal ?? '-' }}</td>
                                <td>{{ $absensi->where('id_kategori_absensi', $kategori->id)->count() }}</td>
                                <td>
                                    {{-- Lihat peserta absensi --}}
                                    <a href="{{ route('operator.absensi.show', $kategori->id) }}" class="btn btn-info btn-sm">
                                        <i class="bi bi-eye"></i> Lihat Peserta
                                    </a>
                                    {{-- Scan QR --}}
                                    <a href="{{ route('operator.absensi.scan', $kategori->id) }}" class="btn btn-success btn-sm">
                                        <i class="bi bi-qr-code-scan"></i> Scan QR
                                    </a>
                                    {{-- Tombol Edit Sesi --}}
                                    <button class="btn btn-secondary btn-sm btn-edit-sesi"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editSesiModal"
                                        data-id="{{ $kategori->id }}"
                                        data-nama="{{ $kategori->nama }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    {{-- Tombol Hapus Sesi --}}
                                    <button class="btn btn-danger btn-sm btn-hapus-sesi"
                                        data-bs-toggle="modal"
                                        data-bs-target="#hapusSesiModal"
                                        data-id="{{ $kategori->id }}"
                                        data-nama="{{ $kategori->nama }}">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ====== DATA PESERTA ====== --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Data Peserta</h5>
                <div>
                    {{-- Export Excel --}}
                    {{-- Export Peserta (langsung jalan tanpa modal) --}}
                    <a href="{{ route('operator.peserta.export', $kegiatan->id) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </a>

                    {{-- Import Peserta (pakai modal) --}}
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#importPesertaModal">
                        <i class="bi bi-upload"></i> Import Peserta
                    </button>
                    {{-- Tombol Tambah Peserta --}}
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
                            <th>Status Hadir</th>
                            <th>QR Code</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kegiatan->peserta as $i => $p)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $p->nama }}</td>
                                <td>
                                    @foreach($kategoriAbsensi as $kategori)
                                        @php
                                            $sudahHadir = $absensi
                                                ->where('id_peserta', $p->id)
                                                ->where('id_kategori', $kategori->id)
                                                ->count() > 0;
                                        @endphp

                                        @if($sudahHadir)
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        @else
                                            <i class="bi bi-x-circle-fill text-danger"></i>
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    @if($p->qrcode)
                                        <img src="{{ asset('storage/'.$p->qrcode) }}" alt="QR Code {{ $p->nama }}" width="50">
                                    @else
                                        <span class="text-muted">QR belum tersedia</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Absen Manual --}}
                                    <button class="btn btn-success btn-sm btn-absen-manual" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#absenManualModal"
                                        data-id="{{ $p->id }}"
                                        data-nama="{{ $p->nama }}">
                                        <i class="bi bi-check-circle"></i> Absen Manual
                                    </button>

                                    <button class="btn btn-danger btn-sm btn-hapus-peserta"
                                        data-bs-toggle="modal"
                                        data-bs-target="#hapusPesertaModal"
                                        data-id="{{ $p->id }}"
                                        data-nama="{{ $p->nama }}">
                                        <i class="bi bi-trash"></i>
                                    </button>


                                    {{-- Download QR Code --}}
                                    <a href="{{ route('operator.peserta.download_qrcode', $p->id) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-download"></i> QR Code
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



<!-- Modal Tambah Sesi -->
<div class="modal fade" id="tambahSesiModal" tabindex="-1" aria-labelledby="tambahSesiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('operator.kegiatan.kategori.store', $kegiatan->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahSesiModalLabel">Tambah Sesi Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Sesi</label>
                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Contoh: Sesi 1 - Pagi" required>
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
                    <div class="mb-3">
                        <label for="namaSesi" class="form-label">Nama Sesi</label>
                        <input type="text" class="form-control" id="namaSesi" name="nama" required>
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

<div class="modal fade" id="hapusSesiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formHapusSesi" method="POST">
            @csrf
            @method('DELETE')
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
                        <input type="email" name="email" id="email" class="form-control" placeholder="Masukkan email peserta" required>
                    </div>

                    {{-- Nomor HP --}}
                    <div class="mb-3">
                        <label for="no_hp" class="form-label">No. HP</label>
                        <input type="text" name="no_hp" id="no_hp" class="form-control" placeholder="Masukkan nomor HP" required>
                    </div>

                    {{-- Program Studi --}}
                    <div class="mb-3">
                        <label for="prodi" class="form-label">Program Studi</label>
                        <input type="text" name="prodi" id="prodi" class="form-control" placeholder="Masukkan program studi" required>
                    </div>

                    {{-- NIM --}}
                    <div class="mb-3">
                        <label for="nim" class="form-label">NIM</label>
                        <input type="text" name="nim" id="nim" class="form-control" placeholder="Masukkan NIM" required>
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
        <form id="formAbsenManual" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Absensi Manual - <span id="pesertaNama"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_peserta" id="idPeserta">
                    <div class="mb-3">
                        <label for="id_kategori" class="form-label">Pilih Sesi Absensi</label>
                        <select name="id_kategori" class="form-select" required>
                            <option value="">-- Pilih Sesi --</option>
                            @foreach($kategoriAbsensi as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <div>
                        <button type="submit" formaction="{{ route('operator.absensi.cancel', 0) }}" 
                            formmethod="POST" class="btn btn-warning" id="btnCancelAbsen">
                            @csrf
                            <i class="bi bi-x-circle"></i> Batalkan Absensi
                        </button>
                        <button type="submit" formaction="{{ route('operator.absensi.manual', 0) }}" 
                            class="btn btn-success" id="btnSaveAbsen">
                            <i class="bi bi-check-circle"></i> Simpan Absensi
                        </button>
                    </div>
                </div>
            </div>
        </form>
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
<script>
    const idKegiatan = {{ $kegiatan->id }};
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Edit sesi
        document.querySelectorAll(".btn-edit-sesi").forEach(btn => {
            btn.addEventListener("click", function () {
                let id = this.dataset.id;
                let nama = this.dataset.nama;

                // isi input di modal
                document.getElementById("namaSesi").value = nama;

                // ubah action form sesuai ID
                document.getElementById("formEditSesi")
                    .setAttribute("action", `${idKegiatan}/kategori/update/${id}`);
            });
        });

        // Hapus sesi
        document.querySelectorAll(".btn-hapus-sesi").forEach(btn => {
            btn.addEventListener("click", function () {
                let id = this.dataset.id;
                let nama = this.dataset.nama;

                // tampilkan nama di modal
                document.getElementById("hapusSesiNama").textContent = nama;

                // ubah action form sesuai ID
                document.getElementById("formHapusSesi")
                    .setAttribute("action", `${idKegiatan}/kategori/delete/${id}`);
            });
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Absen Manual
        document.querySelectorAll(".btn-absen-manual").forEach(btn => {
            btn.addEventListener("click", function () {
                let id = this.dataset.id;
                let nama = this.dataset.nama;

                document.getElementById("pesertaNama").textContent = nama;
                document.getElementById("idPeserta").value = id;

                // Ganti action form sesuai ID
                document.getElementById("btnSaveAbsen").setAttribute("formaction", `/operator/absensi/manual/${id}`);
                document.getElementById("btnCancelAbsen").setAttribute("formaction", `/operator/absensi/cancel/${id}`);
            });
        });

        // Hapus Peserta
        document.querySelectorAll(".btn-hapus-peserta").forEach(btn => {
            btn.addEventListener("click", function () {
                let id = this.dataset.id;
                let nama = this.dataset.nama;

                document.getElementById("hapusPesertaNama").textContent = nama;
                document.getElementById("formHapusPeserta").setAttribute("action", `/operator/peserta/delete/${id}`);
            });
        });
    });
</script>

@endsection
