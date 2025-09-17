@extends('layouts.app')

@section('title', 'Kelola Peserta')
@section('page', 'Kelola Peserta')

@section('content')
<div class="container-fluid">
    @include('components.alert')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Daftar Akun Peserta</h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahPesertaModal">
                <i class="bi bi-plus-circle"></i> Tambah Akun Peserta
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                {{-- ✅ DITAMBAHKAN: ID unik "pesertaTable" untuk DataTables --}}
                <table id="pesertaTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pesertas as $peserta)
                            <tr>
                                {{-- ✅ DIUBAH: Penomoran sekarang menggunakan $loop->iteration --}}
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $peserta->name }}</td>
                                <td>{{ $peserta->username }}</td>
                                <td>{{ $peserta->email }}</td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm btn-edit" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editPesertaModal"
                                        data-id="{{ $peserta->id }}"
                                        data-name="{{ $peserta->name }}"
                                        data-username="{{ $peserta->username }}"
                                        data-email="{{ $peserta->email }}">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm btn-hapus"
                                        data-bs-toggle="modal"
                                        data-bs-target="#hapusPesertaModal"
                                        data-id="{{ $peserta->id }}"
                                        data-name="{{ $peserta->name }}">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data akun peserta.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- ✅ DIHAPUS: Blok pagination Laravel tidak lagi diperlukan --}}
        </div>
    </div>
</div>

<!-- MODAL TAMBAH PESERTA -->
<div class="modal fade" id="tambahPesertaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.peserta.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Akun Peserta Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT PESERTA -->
<div class="modal fade" id="editPesertaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formEditPeserta" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Peserta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <hr>
                    <p class="text-muted small">Kosongkan password jika tidak ingin mengubahnya.</p>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="edit_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="edit_password_confirmation" name="password_confirmation">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL HAPUS PESERTA -->
<div class="modal fade" id="hapusPesertaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formHapusPeserta" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus akun peserta <strong id="hapusPesertaNama"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
{{-- ✅ DITAMBAHKAN: Script untuk mengaktifkan DataTables --}}
<script>
    $(document).ready(function() {
        $('#pesertaTable').DataTable({
            "responsive": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"
            }
        });
    });
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Logika untuk Modal Edit (tidak berubah)
    const editPesertaModal = document.getElementById('editPesertaModal');
    if (editPesertaModal) {
        editPesertaModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const form = editPesertaModal.querySelector('#formEditPeserta');
            
            let actionUrl = "{{ route('admin.peserta.update', ['user' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', id);
            form.setAttribute('action', actionUrl);

            form.querySelector('#edit_name').value = button.getAttribute('data-name');
            form.querySelector('#edit_username').value = button.getAttribute('data-username');
            form.querySelector('#edit_email').value = button.getAttribute('data-email');
        });
    }

    // Logika untuk Modal Hapus (tidak berubah)
    const hapusPesertaModal = document.getElementById('hapusPesertaModal');
    if (hapusPesertaModal) {
        hapusPesertaModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-name');
            const formHapus = hapusPesertaModal.querySelector('#formHapusPeserta');

            hapusPesertaModal.querySelector('#hapusPesertaNama').textContent = nama;
            let actionUrl = "{{ route('admin.peserta.destroy', ['user' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', id);
            formHapus.setAttribute('action', actionUrl);
        });
    }
});
</script>
@endpush

