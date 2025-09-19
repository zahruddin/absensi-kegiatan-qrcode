<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Peserta</th>
            {{-- <th>NIM</th> --}}
            <th>Kelompok</th>
            {{-- Buat header kolom untuk setiap nama sesi --}}
            @foreach ($sesiAbsensi as $sesi)
                <th>{{ $sesi->nama }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($peserta as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->nama }}</td>
                {{-- <td>{{ $p->nim ?? '-' }}</td> --}}
                <td>{{ $p->kelompok ?? '-' }}</td>
                
                {{-- Untuk setiap sesi, cek apakah peserta ini hadir --}}
                @foreach($sesiAbsensi as $sesi)
                    <td>
                        {{-- Cek apakah ID peserta ada di dalam "peta" kehadiran, dan apakah ia hadir di sesi ini --}}
                        @if(isset($kehadiranPeserta[$p->id]) && $kehadiranPeserta[$p->id]->contains($sesi->id))
                            v
                        @else
                            {{-- Biarkan sel kosong jika tidak hadir --}}
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>