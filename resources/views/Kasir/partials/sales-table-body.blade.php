@php $no = 1; @endphp
@foreach($sales as $sale)
<tr>
    <td>{{ $no++ }}</td>
    <td>{{ $sale->created_at->format('d-m-Y H:i') }}</td>
    <td>{{ $sale->meja->nama_meja }}</td>
    <td><span class="text-success">Rp {{ number_format($sale->total_harga, 0, ',', '.') }}</span></td>
    <td><span class="text-danger">Rp {{ number_format($sale->total_diskon, 0, ',', '.') }}</span></td>
    <td><strong>Rp {{ number_format($sale->total_bayar, 0, ',', '.') }}</strong></td>
    <td><span class="badge bg-primary">{{ ucfirst($sale->metode_bayar) }}</span></td>
    <td><span class="badge bg-{{ $sale->status_bayar == 'lunas' ? 'success' : ($sale->status_bayar == 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($sale->status_bayar) }}</span></td>
    <td><span class="badge bg-{{ $sale->status_pesanan == 'selesai' ? 'success' : ($sale->status_pesanan == 'sedang_diproses' ? 'warning' : 'danger') }}">{{ ucfirst($sale->status_pesanan) }}</span></td>
    <td>
        @if ($sale->status_pesanan == 'sedang_diproses' || $sale->status_pesanan == 'belum_diproses')
            <button class="btn btn-success btn-sm btn-proses-pesanan" data-id="{{ $sale->id }}">
                <i data-feather="check-circle"></i> Tandai Selesai
            </button>
        @else
            <span class="text-muted">Selesai</span>
        @endif

        <button class="btn btn-sm btn-outline-primary btn-detail" type="button" data-bs-toggle="collapse" data-bs-target="#detail-{{ $sale->id }}" aria-expanded="false">
            <i data-feather="eye"></i> Detail
        </button>
    </td>
</tr>
<tr class="collapse fade" id="detail-{{ $sale->id }}">
    <td colspan="12">
        <div class="p-2 rounded">
            <h6 class="mb-2">🛒 Detail Produk {{ $sale->created_at->format('d-m-Y H:i') }}</h6>
            <table class="table table-sm">
                <thead>
                    <tr class="table">
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                        <th>Diskon</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->details as $detail)
                        <tr>
                            <td>{{ $detail->nama_produk }}</td>
                            <td>Rp {{ number_format($detail->harga_produk, 0, ',', '.') }}</td>
                            <td>{{ $detail->jumlah }}</td>
                            <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($detail->diskon, 0, ',', '.') }}</td>
                            <td><strong>Rp {{ number_format($detail->total, 0, ',', '.') }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </td>
</tr>
@endforeach
