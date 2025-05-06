@extends('layouts.app')

@section('title', 'Kelola Produk Admin | TrackBooth')

@section('page', 'Kelola Produk')
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
                                <th>Nama Produk</th>
                                <th>Gambar</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = $products->firstItem(); // Ambil nomor item pertama di halaman ini
                            @endphp
                            @foreach($products as $index => $product)
                                <tr>
                                    <td>{{ $no++ }}</td> {{-- Nomor akan bertambah setiap iterasi --}}
                                    <td>{{ $product->nama_produk }}</td>
                                    <td>
                                        @php
                                            $gambarPath = public_path($product->gambar);
                                            $gambarURL = file_exists($gambarPath) && !empty($product->gambar) ? asset($product->gambar) : null;
                                        @endphp
                                    
                                        @if($gambarURL)
                                            <img src="{{ $gambarURL }}" class="img-thumbnail" width="50" height="50" alt="Produk">
                                        @else
                                            <span class="text-muted">
                                                <i class="bi bi-image" style="font-size: 1.5rem;"></i>
                                            </span>
                                        @endif
                                    </td> 
                                    <td>Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</td>
                                    <td>{{ $product->stok_produk }}</td>
                                    <td>{{ Str::limit($product->deskripsi, 50, '...') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $product->status == 'aktif' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($product->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-warning btn-sm edit-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editProdukModal" 
                                        data-id="{{ $product->id }}" 
                                        data-nama="{{ $product->nama_produk }}" 
                                        data-harga="{{ $product->harga_produk }}" 
                                        data-stok="{{ $product->stok_produk }}" 
                                        data-deskripsi="{{ $product->deskripsi }}"
                                        data-gambar="{{ asset($product->gambar) }}"
                                        data-status="{{ $product->status }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button class="btn btn-danger btn-sm deleteUser deleteProduct" data-id="{{ $product->id }}">
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
                    {{ $products->links('vendor.pagination.bootstrap-5') }}
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
                    <form action="{{ route('admin.kelolaproduk.add') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nama_produk" class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" name="nama_produk" required>
                            </div>
                            <div class="mb-3">
                                <label for="harga_produk" class="form-label">Harga</label>
                                <input type="number" class="form-control" name="harga_produk" required>
                            </div>
                            <div class="mb-3">
                                <label for="stok_produk" class="form-label">Stok</label>
                                <input type="number" class="form-control" name="stok_produk" required>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="deskripsi" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="gambar" class="form-label">Gambar Produk</label>
                                <input type="file" class="form-control" name="gambar" id="gambarInput" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, JPEG (Max 2MB)</small>
                                <div class="mt-2">
                                    <img id="gambarPreview" src="#" alt="Preview Gambar" class="d-none rounded img-thumbnail" width="100">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
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
                                <label for="edit_nama_produk" class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" id="edit_nama_produk" name="nama_produk" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_harga_produk" class="form-label">Harga</label>
                                <input type="number" class="form-control" id="edit_harga_produk" name="harga_produk" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_stok_produk" class="form-label">Stok</label>
                                <input type="number" class="form-control" id="edit_stok_produk" name="stok_produk" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="edit_gambar" class="form-label">Gambar Produk</label>
                                <input type="file" class="form-control" id="edit_gambar" name="gambar" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, JPEG (Max 2MB)</small>
                                <div class="mt-2">
                                    <img id="edit_gambarPreview" src="#" alt="Preview Gambar" class="d-none rounded img-thumbnail" width="100">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="edit_status" class="form-label">Status</label>
                                <select class="form-select" id="edit_status" name="status">
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
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
                url: `/admin/kelolaproduk/${deleteProductId}`,
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
    document.getElementById('gambarInput').addEventListener('change', function(event) {
        let input = event.target;
        let preview = document.getElementById('gambarPreview');
        
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                let id_product = this.getAttribute('data-id');
                let nama = this.getAttribute('data-nama');
                let harga = this.getAttribute('data-harga');
                let stok = this.getAttribute('data-stok');
                let deskripsi = this.getAttribute('data-deskripsi');
                let gambar = this.getAttribute('data-gambar');
                let status = this.getAttribute('data-status');

                // Isi nilai form dengan data produk yang diklik
                document.getElementById('edit_id').value = id_product;
                document.getElementById('edit_nama_produk').value = nama;
                document.getElementById('edit_harga_produk').value = harga;
                document.getElementById('edit_stok_produk').value = stok;
                document.getElementById('edit_deskripsi').value = deskripsi;
                document.getElementById('edit_status').value = status;

                // Cek dan atur preview gambar dengan benar
                let gambarPreview = document.getElementById('edit_gambarPreview');
                if (gambar && gambar !== "#" && gambar !== "null") {
                    gambarPreview.src = gambar.startsWith('/storage/') || gambar.startsWith('http') ? gambar : `/storage/${gambar}`;
                    gambarPreview.classList.remove('d-none');
                } else {
                    gambarPreview.classList.add('d-none');
                    gambarPreview.src = "#"; // Reset gambar jika tidak ada
                }
            //     console.log("Form action:", document.getElementById('editProdukForm').getAttribute('action'));
            // console.log("Form method:", document.getElementById('editProdukForm').getAttribute('method'));

                // Ubah action form untuk update produk berdasarkan ID
                document.getElementById('editProdukForm').setAttribute('action', `/admin/kelolaproduk/${id_product}`);
            });
        });

        // Preview gambar yang dipilih sebelum upload
        document.getElementById('edit_gambar').addEventListener('change', function(event) {
            let gambarPreview = document.getElementById('edit_gambarPreview');
            let file = event.target.files[0];

            if (file) {
                let url = URL.createObjectURL(file);
                gambarPreview.src = url;
                gambarPreview.classList.remove('d-none');
            } else {
                gambarPreview.classList.add('d-none');
                gambarPreview.src = "#"; // Reset jika tidak ada gambar
            }
        });
    });
</script>
@endsection
