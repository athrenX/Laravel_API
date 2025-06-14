<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pemesanan</title>
    <style>
        body { font-family: sans-serif; background: #f3f4f6; padding: 30px; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        th, td { padding: 12px; border: 1px solid #e5e7eb; text-align: left; }
        th { background: #3b82f6; color: white; }
        .btn { padding: 8px 12px; background: #10b981; color: white; text-decoration: none; border-radius: 5px; display: inline-block;}
        .btn-danger { background: #ef4444; }
        .btn-danger:hover { background: #dc2626; }
        .btn-edit { background: #f59e0b; }
        .btn-edit:hover { background: #d97706; }
        .top-bar { margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        h2 { color: #1f2937; }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: bold;
            color: white;
            text-transform: capitalize;
        }
        .status-pending { background: #6b7280; } /* abu-abu */
        .status-menunggu-pembayaran { background: #f59e0b; } /* orange */
        .status-dibayar { background: #10b981; } /* hijau */
        .status-diproses { background: #3b82f6; } /* biru */
        .status-selesai { background: #22c55e; } /* hijau tua */
        .status-dibatalkan { background: #ef4444; } /* merah */
        .search-filter {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            align-items: center;
        }
        .search-filter input[type="text"], .search-filter select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .search-filter button {
            padding: 8px 15px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <h2>Daftar Pemesanan</h2>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;padding:10px;color:#166534;border-radius:5px;margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#fee2e2;padding:10px;color:#991b1b;border-radius:5px;margin-bottom:15px;">
            {{ session('error') }}
        </div>
    @endif

    <form method="GET" action="{{ route('admin.pemesanan.index') }}" class="search-filter">
        <input type="text" name="search" placeholder="Cari Destinasi/User..." value="{{ request('search') }}">
        <select name="status">
            <option value="all">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="menunggu pembayaran" {{ request('status') == 'menunggu pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
            <option value="dibayar" {{ request('status') == 'dibayar' ? 'selected' : '' }}>Dibayar</option>
            <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
            <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
        <button type="submit">Filter</button>
        <a href="{{ route('admin.pemesanan.index') }}" class="btn">Reset</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Destinasi</th>
                <th>Kendaraan</th>
                <th>Kursi</th>
                <th>Peserta</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pemesanans as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->user->name ?? 'N/A' }}</td>
                    <td>{{ $p->destinasi->nama ?? 'N/A' }}</td>
                    <td>{{ $p->kendaraan->jenis ?? 'N/A' }} ({{ $p->kendaraan->tipe ?? 'N/A' }})</td>
                    <td>{{ implode(', ', $p->selected_seats ?? []) }}</td>
                    <td>{{ $p->jumlah_peserta }}</td>
                    <td>Rp{{ number_format($p->total_harga, 0, ',', '.') }}</td>
                    <td>
                        <span class="status-badge status-{{ Str::slug($p->status) }}">
                            {{ $p->status }}
                        </span>
                    </td>
                    <td>{{ $p->tanggal_pemesanan->format('d M Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.pemesanan.edit', $p->id) }}" class="btn btn-edit">Edit Status</a>
                        <form action="{{ route('admin.pemesanan.destroy', $p->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pemesanan ini? Kursi akan dikembalikan jika belum dibatalkan.')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 20px;">Tidak ada data pemesanan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
