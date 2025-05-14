<h3>Struk Pembayaran</h3>
<p>Tanggal: {{ $sale->created_at->format('d-m-Y H:i') }}</p>
<p>Meja: {{ $sale->meja->nama_meja ?? '-' }}</p>

<table>
    <thead>
        <tr>
            <th>Produk</th>
            <th>Harga</th>
            <th>Jumlah</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sale->details as $item)
        <tr>
            <td>{{ $item->nama_produk }}</td>
            <td>Rp {{ number_format($item->harga_produk, 0, ',', '.') }}</td>
            <td>{{ $item->jumlah }}</td>
            <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<p><strong>Total Bayar:</strong> Rp {{ number_format($sale->total_bayar, 0, ',', '.') }}</p>
