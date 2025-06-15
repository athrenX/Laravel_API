<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemesanan #{{ $pemesanan->id }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; margin-bottom: 20px; text-align: center; }
        .detail-item { margin-bottom: 10px; }
        .detail-item strong { display: inline-block; width: 150px; }
        .status { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; text-transform: uppercase; color: white; }
        .status-pending { background-color: #f39c12; }
        .status-menunggu { background-color: #e74c3c; }
        .status-dibayar { background-color: #2ecc71; }
        .status-diproses { background-color: #3498db; }
        .status-selesai { background-color: #9b59b6; }
        .status-dibatalkan { background-color: #95a5a6; }
        .btn { display: inline-block; padding: 8px 16px; background-color: #3498db; color: white; text-decoration: none; border-radius: 4px; transition: background-color 0.3s; margin-top: 20px; }
        .btn:hover { background-color: #2980b9; }
        .img-thumbnail { width: 100px; height: 100px; object-fit: cover; border-radius: 4px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Detail Pemesanan #{{ $pemesanan->id }}</h1>

        <div class="detail-item">
            <strong>Pelanggan:</strong> {{ $pemesanan->user->name }} ({{ $pemesanan->user->email }})
        </div>
        <div class="detail-item">
            <strong>Destinasi:</strong> {{ $pemesanan->destinasi->nama }} ({{ $pemesanan->destinasi->lokasi }})
            @if($pemesanan->destinasi->gambar)
                <br><img src="{{ $pemesanan->destinasi->gambar }}" alt="Gambar Destinasi" class="img-thumbnail">
            @endif
        </div>
        <div class="detail-item">
            <strong>Kendaraan:</strong> {{ $pemesanan->kendaraan->jenis }} ({{ $pemesanan->kendaraan->tipe }}) - Kapasitas: {{ $pemesanan->kendaraan->kapasitas }}
            @if($pemesanan->kendaraan->gambar)
                <br><img src="{{ $pemesanan->kendaraan->gambar }}" alt="Gambar Kendaraan" class="img-thumbnail">
            @endif
        </div>
        <div class="detail-item">
            <strong>Kursi Dipilih:</strong> {{ implode(', ', $pemesanan->selected_seats) }}
        </div>
        <div class="detail-item">
            <strong>Jumlah Peserta:</strong> {{ $pemesanan->jumlah_peserta }}
        </div>
        <div class="detail-item">
            <strong>Tanggal Pemesanan:</strong> {{ \Carbon\Carbon::parse($pemesanan->tanggal_pemesanan)->format('d M Y H:i') }}
        </div>
        @if($pemesanan->expired_at)
        <div class="detail-item">
            <strong>Kadaluarsa Pembayaran:</strong> {{ \Carbon\Carbon::parse($pemesanan->expired_at)->format('d M Y H:i') }} ({{ \Carbon\Carbon::parse($pemesanan->expired_at)->diffForHumans() }})
        </div>
        @endif
        <div class="detail-item">
            <strong>Total Harga:</strong> Rp{{ number_format($pemesanan->total_harga, 0, ',', '.') }}
        </div>
        <div class="detail-item">
            <strong>Status:</strong>
            @php
                $statusClass = '';
                switch($pemesanan->status) {
                    case 'pending': $statusClass = 'status-pending'; break;
                    case 'menunggu pembayaran': $statusClass = 'status-menunggu'; break;
                    case 'dibayar': $statusClass = 'status-dibayar'; break;
                    case 'diproses': $statusClass = 'status-diproses'; break;
                    case 'selesai': $statusClass = 'status-selesai'; break;
                    case 'dibatalkan': $statusClass = 'status-dibatalkan'; break;
                }
            @endphp
            <span class="status {{ $statusClass }}">{{ $pemesanan->status }}</span>
        </div>

        <a href="{{ route('admin.pemesanan.index') }}" class="btn">Kembali ke Daftar Pemesanan</a>
    </div>
</body>
</html>
