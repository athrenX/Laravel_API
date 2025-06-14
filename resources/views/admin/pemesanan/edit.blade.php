<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Pemesanan</title>
    <style>
        body { font-family: sans-serif; background: #f3f4f6; padding: 40px; }
        form { max-width: 600px; background: white; padding: 30px; border-radius: 10px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 5px; }
        button { margin-top: 20px; padding: 12px; width: 100%; background: #3b82f6; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; }
        button:hover { background: #2563eb; }
        .error { color: #dc2626; margin-top: 5px; font-size: 14px; }
        h2 { text-align: center; color: #1f2937; margin-bottom: 20px; }
        .info-group { margin-bottom: 15px; border: 1px solid #eee; padding: 10px; border-radius: 5px; background-color: #f9f9f9; }
        .info-group strong { display: block; margin-bottom: 5px; color: #333; }
        .info-group p { margin: 0; color: #555; }
    </style>
</head>
<body>

    <form action="{{ route('admin.pemesanan.update', $pemesanan->id) }}" method="POST">
        @csrf
        @method('PUT')
        <h2>Edit Pemesanan #{{ $pemesanan->id }}</h2>

        <div class="info-group">
            <strong>User:</strong> <p>{{ $pemesanan->user->name ?? 'N/A' }}</p>
        </div>
        <div class="info-group">
            <strong>Destinasi:</strong> <p>{{ $pemesanan->destinasi->nama ?? 'N/A' }}</p>
        </div>
        <div class="info-group">
            <strong>Kendaraan:</strong> <p>{{ $pemesanan->kendaraan->jenis ?? 'N/A' }} ({{ $pemesanan->kendaraan->tipe ?? 'N/A' }})</p>
        </div>
        <div class="info-group">
            <strong>Kursi Terpilih:</strong> <p>{{ implode(', ', $pemesanan->selected_seats ?? []) }}</p>
        </div>
        <div class="info-group">
            <strong>Jumlah Peserta:</strong> <p>{{ $pemesanan->jumlah_peserta }}</p>
        </div>
        <div class="info-group">
            <strong>Total Harga:</strong> <p>Rp{{ number_format($pemesanan->total_harga, 0, ',', '.') }}</p>
        </div>
        <div class="info-group">
            <strong>Tanggal Pemesanan:</strong> <p>{{ $pemesanan->tanggal_pemesanan->format('d M Y H:i') }}</p>
        </div>

        <label for="status">Status Pemesanan</label>
        <select name="status" id="status">
            <option value="pending" {{ $pemesanan->status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="menunggu pembayaran" {{ $pemesanan->status == 'menunggu pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
            <option value="dibayar" {{ $pemesanan->status == 'dibayar' ? 'selected' : '' }}>Dibayar</option>
            <option value="diproses" {{ $pemesanan->status == 'diproses' ? 'selected' : '' }}>Diproses</option>
            <option value="selesai" {{ $pemesanan->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
            <option value="dibatalkan" {{ $pemesanan->status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
        @error('status') <div class="error">{{ $message }}</div> @enderror

        <button type="submit">Update Status</button>
    </form>

</body>
</html>
