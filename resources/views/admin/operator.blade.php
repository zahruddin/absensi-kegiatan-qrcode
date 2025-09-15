@extends('layouts.app')

@section('title', 'Kelola Operator')
@section('page', 'Kelola Operator')

@section('content')
<div class="container-fluid">
    @include('components.alert')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Daftar Operator</h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahOperatorModal">
                <i class="bi bi-plus-circle"></i> Tambah Operator
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
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
                        @forelse ($operators as $operator)
                            <tr>
                                <td>{{ $loop->iteration + $operators->firstItem() - 1 }}</td>
                                <td>{{ $operator->name }}</td>
                                <td>{{ $operator->username }}</td>
                                <td>{{ $operator->email }}</td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm btn-edit" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editOperatorModal"
                                        data-id="{{ $operator->id }}"
                                        data-name="{{ $operator->name }}"
                                        data-username="{{ $operator->username }}"
                                        data-email="{{ $operator->email }}">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm btn-hapus"
                                        data-bs-toggle="modal"
                                        data-bs-target="#hapusOperatorModal"
                                        data-id="{{ $operator->id }}"
                                        data-name="{{ $operator->name }}">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data operator.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                {{ $operators->links() }}
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH OPERATOR -->
<div class="modal fade" id="tambahOperatorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.operator.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Operator Baru</h5>
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

<!-- MODAL EDIT OPERATOR -->
<div class="modal fade" id="editOperatorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formEditOperator" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Operator</h5>
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

<!-- MODAL HAPUS OPERATOR -->
<div class="modal fade" id="hapusOperatorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formHapusOperator" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus operator <strong id="hapusOperatorNama"></strong>?</p>
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
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Logika untuk Modal Edit
    const editOperatorModal = document.getElementById('editOperatorModal');
    if (editOperatorModal) {
        editOperatorModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const form = editOperatorModal.querySelector('#formEditOperator');
            
            let actionUrl = "{{ route('admin.operator.update', ['user' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', id);
            form.setAttribute('action', actionUrl);

            form.querySelector('#edit_name').value = button.getAttribute('data-name');
            form.querySelector('#edit_username').value = button.getAttribute('data-username');
            form.querySelector('#edit_email').value = button.getAttribute('data-email');
        });
    }

    // Logika untuk Modal Hapus
    const hapusOperatorModal = document.getElementById('hapusOperatorModal');
    if (hapusOperatorModal) {
        hapusOperatorModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-name');
            const formHapus = hapusOperatorModal.querySelector('#formHapusOperator');

            hapusOperatorModal.querySelector('#hapusOperatorNama').textContent = nama;
            let actionUrl = "{{ route('admin.operator.destroy', ['user' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', id);
            formHapus.setAttribute('action', actionUrl);
        });
    }
});
</script>
@endpush
