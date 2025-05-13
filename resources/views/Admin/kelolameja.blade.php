@extends('layouts.app')

@section('title', 'Kelola Meja Admin | WELIJO')

@section('page', 'Kelola Meja')
@section('content')
<div class="app-content">
    <div class="container-fluid">
        {{-- ALERT --}}
    
        @include('components.alert')
        {{-- END ALERT --}}

        
        {{-- TABEL card --}}
        <div class="card">
            <div class="card-body">
                <div class="mb-2">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahProduct">
                        Tambah Product
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>qr code</th>
                                <th>Nama Meja</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = $tables->firstItem(); // Ambil nomor item pertama di halaman ini
                            @endphp
                            @foreach($tables as $index => $table)
                                <tr>
                                    <td>{{ $no++ }}</td> {{-- Nomor akan bertambah setiap iterasi --}}
                                    <td>
                                        @if ($table->qr_code)
                                        <img src="{{ asset('storage/' . $table->qr_code) }}" alt="QR Code" width="75">
                                        @else
                                            <p>QR Code belum tersedia</p>
                                        @endif
                                    </td> 
                                    <td>{{ $table->nama_meja }}</td>
                                    <td>
                                        <span class="badge bg-{{ $table->status == 'ready' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($table->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-warning btn-sm edit-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editProdukModal" 
                                        data-id="{{ $table->id }}" 
                                        data-nama="{{ $table->nama_meja }}" 
                                        data-gambar="{{ asset($table->qr_code) }}"
                                        data-status="{{ $table->status }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button class="btn btn-danger btn-sm deleteUser deleteProduct" data-id="{{ $table->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>                                            
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="d-flex justify-content-end mt-3">
                    {{ $tables->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>
        <!-- /.TABEL card -->
        {{-- MODAL Tambah Produk --}}
        <div class="modal fade" id="modalTambahProduct" tabindex="-1" aria-labelledby="modalTambahProductLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahProductLabel">Tambah Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.kelolameja.add') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nama_meja" class="form-label">Nama Meja</label>
                                <input type="text" class="form-control" name="nama_meja" required>
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
        {{-- MODAL UPDATE/EDIT Produk --}}
        <div class="modal fade" id="editProdukModal" tabindex="-1" aria-labelledby="editProdukLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProdukLabel">Update Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editProdukForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="edit_id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_nama_meja" class="form-label">Nama Meja</label>
                                <input type="text" class="form-control" id="edit_nama_meja" name="nama_meja" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_status" class="form-label">Status</label>
                                <select class="form-select" id="edit_status" name="status">
                                    <option value="ready">ready</option>
                                    <option value="ordered">Ordered</option>
                                </select>
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
        {{-- MODAL Konfirmasi Hapus --}}
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus produk ini?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="bataldelete" data-bs-dismiss="modal">Batal</button>
                        <button id="confirmDeleteBtn" class="btn btn-danger">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- end modal notif konfirmasi delete --}}

    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            "paging": true,            // Aktifkan pagination
            "lengthChange": false,     // Hilangkan opsi "show entries"
            "searching": true,         // Aktifkan pencarian
            "ordering": true,          // Aktifkan sorting
            "info": true,              // Tampilkan info jumlah data
            "autoWidth": false,        // Nonaktifkan auto width
            "responsive": true,        // Aktifkan mode responsif
            "language": {
                "paginate": {
                    "first": "Awal",
                    "last": "Akhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                },
                "search": "Cari:",
                "info": "Menampilkan _START_ hingga _END_ dari _TOTAL_ pengguna",
                "infoEmpty": "Tidak ada data",
                "lengthMenu": "Tampilkan _MENU_ pengguna per halaman"
            }
        });
    });
</script>
<script>
    $(document).ready(function () {
        let deleteProductId;

        // Saat tombol hapus diklik, simpan ID user
        $('.deleteUser').click(function () {
            deleteProductId = $(this).data('id');
            $('#confirmDeleteModal').modal('show');
        });
        $('#bataldelete').click(function () {
            $('#confirmDeleteModal').modal('hide');
        });
        $('.close').click(function () {
            $('#confirmDeleteModal').modal('hide');
        });

        $('#confirmDeleteBtn').click(function () {
            $.ajax({
                url: `/admin/kelolameja/${deleteProductId}`,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}" // Kirim token CSRF
                },
                // $('#confirmDeleteModal').modal('hide'), // Tutup modal
                success: function (response) {
                    // alert(response.success); // Tampilkan pesan sukses
                    location.reload(); // Refresh halaman
                },
                error: function (xhr) {
                    alert(xhr.responseJSON.error); // Tampilkan error
                }
            });
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                let id = this.getAttribute('data-id');
                let nama = this.getAttribute('data-nama');
                let status = this.getAttribute('data-status');

                // Isi nilai form dengan data produk yang diklik
                document.getElementById('edit_id').value = id;
                document.getElementById('edit_nama_meja').value = nama;
                document.getElementById('edit_status').value = status;

                // Ubah action form untuk update produk berdasarkan ID
                document.getElementById('editProdukForm').setAttribute('action', `/admin/kelolameja/update/${id}`);
            });
        });
    });
</script>
@endsection
