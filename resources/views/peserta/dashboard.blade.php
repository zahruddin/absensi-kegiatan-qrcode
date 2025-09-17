@extends('layouts.app')

@section('title', 'Dashboard Peserta')
@section('page', 'Dashboard')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Daftar Kegiatan</h1>
    @include('components.alert')

    <div class="row">
        @forelse ($kegiatans as $kegiatan)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $kegiatan->nama }}</h5>
                        <p class="card-text text-muted"><i class="bi bi-calendar-event"></i> {{ \Carbon\Carbon::parse($kegiatan->date)->isoFormat('dddd, D MMMM Y') }}</p>
                        
                        <div class="mt-auto"></div> 
                        
                        @if(isset($pesertaData[$kegiatan->id]))
                            {{-- Tampilan jika sudah terdaftar --}}
                            <div class="d-grid gap-2">
                                <span class="btn btn-success disabled"><i class="bi bi-check-circle-fill"></i> Anda Sudah Terdaftar</span>
                                <button type="button" class="btn btn-outline-primary btn-show-qrcode" 
                                        data-bs-toggle="modal" data-bs-target="#viewQrCodeModal"
                                        data-qrcode-url="{{ asset('storage/' . $pesertaData[$kegiatan->id]->qrcode) }}">
                                    Lihat QR Code
                                </button>
                            </div>
                        @else
                            {{-- ✅ DIUBAH: Tombol "Daftar" sekarang membuka modal --}}
                            <div class="d-grid">
                                <button type="button" class="btn btn-primary btn-daftar" 
                                        data-bs-toggle="modal" data-bs-target="#daftarKegiatanModal"
                                        data-kegiatan-id="{{ $kegiatan->id }}"
                                        data-kegiatan-nama="{{ $kegiatan->nama }}">
                                    <i class="bi bi-person-plus-fill"></i> Daftar Sekarang
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    Saat ini belum ada kegiatan yang tersedia untuk pendaftaran.
                </div>
            </div>
        @endforelse
    </div>
</div>

<div class="modal fade" id="daftarKegiatanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formDaftarKegiatan" method="POST" action=""> {{-- Action diisi oleh JS --}}
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pendaftaran Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Lengkapi data Anda untuk kegiatan <strong id="namaKegiatanDaftar"></strong>.</p>
                    <hr>
                    {{-- Nama (diambil dari akun, tidak bisa diedit) --}}
                    {{-- Nama (diambil dari akun, tidak bisa diedit) --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap (sesuai akun)</label>
                        <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                    </div>
                    {{-- Email (diambil dari akun, tidak bisa diedit) --}}
                    <div class="mb-3">
                        <label class="form-label">Email (sesuai akun)</label>
                        <input type="email" class="form-control" value="{{ Auth::user()->email }}" readonly>
                    </div>
                    <hr>
                    <p class="small text-muted">Silakan isi data di bawah ini jika diperlukan.</p>
                    {{-- Field lain yang bisa diisi --}}
                    <div class="mb-3">
                        <label for="nim" class="form-label">NIM</label>
                        <input type="text" class="form-control" id="nim" name="nim" placeholder="Masukkan NIM Anda (opsional)">
                    </div>
                    <div class="mb-3">
                        <label for="no_hp" class="form-label">No. Handphone</label>
                        <input type="tel" class="form-control" id="no_hp" name="no_hp" placeholder="Masukkan No. HP Anda (opsional)">
                    </div>
                    <div class="mb-3">
                        <label for="prodi" class="form-label">Program Studi</label>
                        <input type="text" class="form-control" id="prodi" name="prodi" placeholder="Contoh: Teknik Informatika">
                    </div>
                     <div class="mb-3">
                        <label for="kelompok" class="form-label">Kelompok</label>
                        <input type="text" class="form-control" id="kelompok" name="kelompok" placeholder="Masukkan nama kelompok (jika ada)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan & Daftar</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="viewQrCodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">QR Code Absensi Anda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p>Tunjukkan QR Code ini kepada panitia saat melakukan absensi.</p>
                <img id="modalQrCodeImage" src="" alt="QR Code Peserta" class="img-fluid">
            </div>
            <div class="modal-footer">
                <a href="#" id="btnDownloadQrCode" class="btn btn-primary w-100" download="qr-code-absensi.png">
                    <i class="bi bi-download"></i> Unduh QR Code
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Skrip untuk modal lihat QR (tidak berubah)
    const viewQrCodeModal = document.getElementById('viewQrCodeModal');
    if (viewQrCodeModal) {
        viewQrCodeModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const qrCodeUrl = button.getAttribute('data-qrcode-url');
            const modalImage = viewQrCodeModal.querySelector('#modalQrCodeImage');
            const downloadButton = viewQrCodeModal.querySelector('#btnDownloadQrCode');
            modalImage.src = qrCodeUrl;
            downloadButton.href = qrCodeUrl;
        });
    }

    // ✅ SCRIPT BARU UNTUK MODAL PENDAFTARAN
    const daftarKegiatanModal = document.getElementById('daftarKegiatanModal');
    if (daftarKegiatanModal) {
        daftarKegiatanModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const kegiatanId = button.getAttribute('data-kegiatan-id');
            const kegiatanNama = button.getAttribute('data-kegiatan-nama');
            const form = daftarKegiatanModal.querySelector('#formDaftarKegiatan');
            
            // Isi nama kegiatan di modal
            daftarKegiatanModal.querySelector('#namaKegiatanDaftar').textContent = kegiatanNama;
            
            // Buat URL action yang benar
            let actionUrl = "{{ route('peserta.kegiatan.register', ['kegiatan' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', kegiatanId);
            
            // Setel action pada form
            form.setAttribute('action', actionUrl);
        });
    }
});
</script>
@endpush