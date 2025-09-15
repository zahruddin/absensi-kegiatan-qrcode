@extends('layouts.app')

@section('title', 'Kelola Kegiatan')
@section('page', 'Kelola Kegiatan')

@section('content')
<div class="app-content">
    <div class="container-fluid">
        @include('components.alert')

        {{-- Kartu Statistik Ringkas --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Total Kegiatan</h5>
                        <h2>{{ $kegiatans->total() }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Kegiatan Aktif</h5>
                        <h2 class="text-success">{{ $kegiatans->where('date', '>=', now()->format('Y-m-d'))->count() }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Kegiatan Selesai</h5>
                        <h2 class="text-muted">{{ $kegiatans->where('date', '<', now()->format('Y-m-d'))->count() }}</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Daftar Kegiatan</h5>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKegiatan">
                    <i class="bi bi-plus-circle"></i> Tambah Kegiatan
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kegiatan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th class="text-center">Jml. Peserta</th>
                                <th class="text-center">Jml. Sesi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kegiatans as $kegiatan)
                                <tr>
                                    <td>{{ $loop->iteration + $kegiatans->firstItem() - 1 }}</td>
                                    <td>
                                        <a href="{{ route('operator.kegiatan.detail', $kegiatan->id) }}" class="fw-bold">{{ $kegiatan->nama }}</a>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($kegiatan->date)->isoFormat('dddd, D MMMM Y') }}</td>
                                    <td>
                                        @if($kegiatan->date > now()->format('Y-m-d'))
                                            <span class="badge bg-secondary">Akan Datang</span>
                                        @elseif($kegiatan->date == now()->format('Y-m-d'))
                                            <span class="badge bg-success">Berlangsung Hari Ini</span>
                                        @else
                                            <span class="badge bg-dark">Selesai</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $kegiatan->peserta_count }}</td>
                                    <td class="text-center">{{ $kegiatan->sesi_absensi_count }}</td>
                                    <td>
                                        <a href="{{ route('operator.kegiatan.detail', $kegiatan->id) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-warning btn-sm btn-edit" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editKegiatanModal" 
                                            data-id="{{ $kegiatan->id }}" 
                                            data-nama="{{ $kegiatan->nama }}" 
                                            data-date="{{ $kegiatan->date }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm btn-hapus"
                                            data-bs-toggle="modal"
                                            data-bs-target="#hapusKegiatanModal"
                                            data-id="{{ $kegiatan->id }}"
                                            data-nama="{{ $kegiatan->nama }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada kegiatan yang dibuat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    {{ $kegiatans->links() }}
                </div>
            </div>
        </div> 
    </div>
</div>

{{-- MODAL TAMBAH KEGIATAN --}}
<div class="modal fade" id="modalTambahKegiatan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('operator.kegiatan.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kegiatan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Kegiatan</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Tanggal Kegiatan</label>
                        <input type="date" class="form-control" name="date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT KEGIATAN --}}
<div class="modal fade" id="editKegiatanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditKegiatan" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Update Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nama" class="form-label">Nama Kegiatan</label>
                        <input type="text" class="form-control" id="edit_nama" name="nama" required>
                    </div>
                     <div class="mb-3">
                        <label for="edit_date" class="form-label">Tanggal Kegiatan</label>
                        <input type="date" class="form-control" id="edit_date" name="date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL HAPUS KEGIATAN --}}
<div class="modal fade" id="hapusKegiatanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formHapusKegiatan" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Konfirmasi Hapus Kegiatan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus kegiatan <strong id="hapusKegiatanNama"></strong>?</p>
                    <p class="text-danger"><i class="bi bi-exclamation-triangle-fill"></i> Semua data sesi, peserta, dan absensi yang terkait akan ikut terhapus secara permanen.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Logika untuk Modal Edit
    const editKegiatanModal = document.getElementById('editKegiatanModal');
    if (editKegiatanModal) {
        editKegiatanModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const date = button.getAttribute('data-date');

            const form = editKegiatanModal.querySelector('#formEditKegiatan');
            let actionUrl = "{{ route('operator.kegiatan.update', 'PLACEHOLDER') }}".replace('PLACEHOLDER', id);
            form.setAttribute('action', actionUrl);

            form.querySelector('#edit_nama').value = nama;
            form.querySelector('#edit_date').value = date;
        });
    }

    // Logika untuk Modal Hapus
    const hapusKegiatanModal = document.getElementById('hapusKegiatanModal');
    if (hapusKegiatanModal) {
        hapusKegiatanModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');

            hapusKegiatanModal.querySelector('#hapusKegiatanNama').textContent = nama;
            const formHapus = hapusKegiatanModal.querySelector('#formHapusKegiatan');
            let actionUrl = "{{ route('operator.kegiatan.destroy', 'PLACEHOLDER') }}".replace('PLACEHOLDER', id);
            formHapus.setAttribute('action', actionUrl);
        });
    }
});
</script>
@endsection
