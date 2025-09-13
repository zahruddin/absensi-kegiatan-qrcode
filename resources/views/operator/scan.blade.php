@extends('layouts.app')

{{-- Title dan Page menggunakan nama kegiatan & sesi --}}
@section('title', 'Scan QR | ' . $sesi_absensi->nama)
@section('page', 'Scan QR Absensi')

@push('styles')
{{-- Tambahkan sedikit style untuk tampilan scanner --}}
<style>
    #reader {
        width: 100%;
        max-width: 500px;
        border: 2px solid #0d6efd;
        border-radius: 8px;
        margin: auto;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header text-center">
                    <h4 class="mb-0">Sesi: {{ $sesi_absensi->nama }}</h4>
                    <small class="text-muted">{{ $sesi_absensi->kegiatan->nama }}</small>
                </div>
                <div class="card-body text-center">
                    <p>Arahkan kamera ke QR Code peserta.</p>
                    
                    <div id="reader"></div>

                    {{-- Form tersembunyi untuk mengirim data ke server --}}
                    <form id="scan-form" action="{{ route('operator.absensi.scan.process') }}" method="POST" class="d-none">
                        @csrf
                        <input type="hidden" name="id_sesi" value="{{ $sesi_absensi->id }}">
                        <input type="hidden" name="token" id="scanned-token">
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ MODAL BARU UNTUK MENAMPILKAN HASIL SCAN --}}
<div class="modal fade" id="scanResultModal" tabindex="-1" aria-labelledby="scanResultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                {{-- Judul modal akan diisi oleh JavaScript --}}
                <h5 class="modal-title" id="scanResultModalLabel">Status Absensi</h5>
            </div>
            <div class="modal-body text-center" style="font-size: 1.1rem;">
                {{-- Pesan hasil akan diisi oleh JavaScript --}}
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
{{-- Library untuk QR Scanner --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ✅ Inisialisasi elemen-elemen modal
    const scanResultModalElement = document.getElementById('scanResultModal');
    const scanResultModal = new bootstrap.Modal(scanResultModalElement);
    const modalTitle = scanResultModalElement.querySelector('.modal-title');
    const modalBody = scanResultModalElement.querySelector('.modal-body');

    const tokenInput = document.getElementById('scanned-token');
    const scanForm = document.getElementById('scan-form');
    let isProcessing = false;

    // ✅ Fungsi ini dijalankan SETELAH modal ditutup
    scanResultModalElement.addEventListener('hidden.bs.modal', function () {
        html5QrcodeScanner.resume(); // Lanjutkan scanning
        isProcessing = false; // Reset flag
    });

    function onScanSuccess(decodedText, decodedResult) {
        if (isProcessing) return;
        isProcessing = true;
        
        html5QrcodeScanner.pause();
        tokenInput.value = decodedText;

        fetch(scanForm.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: new FormData(scanForm)
        })
        .then(response => {
            if (!response.ok) {
                // Jika response error (misal 404, 409), lempar ke catch block
                return Promise.reject(response);
            }
            return response.json();
        })
        .then(data => {
            // ✅ Logika untuk menampilkan modal SUKSES
            modalTitle.className = 'modal-title text-success'; // Ubah warna judul
            modalTitle.textContent = 'Berhasil!';
            modalBody.textContent = data.message;
            scanResultModal.show();
        })
        .catch(errorResponse => {
            // ✅ Logika untuk menampilkan modal GAGAL/PERINGATAN
            errorResponse.json().then(errData => {
                 let message = errData.message || 'Terjadi kesalahan.';
                 let titleText = 'Gagal!';
                 let titleClass = 'modal-title text-danger';

                 if (errData.status === 'warning') {
                     titleText = 'Peringatan';
                     titleClass = 'modal-title text-warning';
                 }
                 
                 modalTitle.className = titleClass;
                 modalTitle.textContent = titleText;
                 modalBody.textContent = message;
                 scanResultModal.show();
            }).catch(() => {
                // Jika error tidak memiliki JSON (misal error jaringan)
                modalTitle.className = 'modal-title text-danger';
                modalTitle.textContent = 'Error';
                modalBody.textContent = 'Gagal terhubung ke server. Periksa koneksi Anda.';
                scanResultModal.show();
            });
        })
        .finally(() => {
            // ✅ Setelah 3 detik, SEMBUNYIKAN modal
            setTimeout(() => {
                scanResultModal.hide();
            }, 3000);
        });
    }

    function onScanFailure(error) {
        // Abaikan error "QR code not found"
    }

    let html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: { width: 250, height: 250 } }, false);
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
});
</script>
@endsection

