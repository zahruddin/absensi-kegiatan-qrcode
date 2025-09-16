<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Mandiri - {{ $kegiatan->nama }}</title>
    {{-- Bootstrap CSS & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        #reader { max-width: 500px; border: 2px dashed #0d6efd; border-radius: 8px; margin: auto; }
        .card { max-width: 600px; }
    </style>
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-sm w-100">
            <div class="card-body text-center p-4">
                <i class="bi bi-calendar-check-fill text-primary" style="font-size: 3rem;"></i>
                <h2 class="mt-3">{{ $kegiatan->nama }}</h2>
                
                @if ($sesiAktif)
                    <p class="text-muted">Anda akan diabsen untuk sesi:</p>
                    <h4 class="mb-4"><span class="badge bg-success">{{ $sesiAktif->nama }}</span></h4>

                    <div id="reader" class="mb-3"></div>
                    <div id="scan-result" class="fw-bold mt-3" style="font-size: 1.2rem;"></div>

                    <form id="scan-form" action="{{ route('scan.mandiri.process') }}" method="POST" class="d-none">
                        @csrf
                        <input type="hidden" name="id_kegiatan" value="{{ $kegiatan->id }}">
                        <input type="hidden" name="token" id="scanned-token">
                    </form>
                @else
                    <div class="alert alert-warning mt-4">
                        <h4 class="alert-heading">Absensi Belum Dibuka</h4>
                        <p class="mb-0">Saat ini tidak ada sesi absensi yang sedang berlangsung. Silakan coba lagi nanti.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Script untuk QR Scanner --}}
    @if($sesiAktif)
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const resultContainer = document.getElementById('scan-result');
        const tokenInput = document.getElementById('scanned-token');
        const scanForm = document.getElementById('scan-form');
        let isProcessing = false;

        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return;
            isProcessing = true;
            html5QrcodeScanner.pause();
            resultContainer.innerHTML = `<span class="text-primary">Memverifikasi...</span>`;
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
                if (!response.ok) return Promise.reject(response);
                return response.json();
            })
            .then(data => {
                resultContainer.innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill"></i> ${data.message}</span>`;
            })
            .catch(errorResponse => {
                errorResponse.json().then(errData => {
                    resultContainer.innerHTML = `<span class="text-danger"><i class="bi bi-x-circle-fill"></i> ${errData.message}</span>`;
                }).catch(() => {
                    resultContainer.innerHTML = `<span class="text-danger">Gagal terhubung.</span>`;
                });
            })
            .finally(() => {
                setTimeout(() => {
                    resultContainer.innerHTML = '';
                    html5QrcodeScanner.resume();
                    isProcessing = false;
                }, 5000); // Tunggu 5 detik sebelum scan lagi
            });
        }

        let html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: { width: 250, height: 250 } }, false);
        html5QrcodeScanner.render(onScanSuccess, (error) => {});
    });
    </script>
    @endif
</body>
</html>

{{-- Tambahkan script ini di bagian @push('scripts') --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnSalin = document.getElementById('btnSalinLinkScanMandiri');
    if(btnSalin) {
        btnSalin.addEventListener('click', function() {
            const link = this.getAttribute('data-link');
            navigator.clipboard.writeText(link).then(() => {
                // Beri feedback visual
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="bi bi-check-all"></i> Link Tersalin!';
                this.classList.remove('btn-info');
                this.classList.add('btn-success');
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.classList.remove('btn-success');
                    this.classList.add('btn-info');
                }, 2000); // Kembalikan setelah 2 detik
            }).catch(err => {
                alert('Gagal menyalin link.');
            });
        });
    }
});
</script>
@endpush
