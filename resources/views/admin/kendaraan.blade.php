<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daftar Kendaraan</title>
    <style>
        body { font-family: sans-serif; background: #f3f4f6; padding: 30px; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        th, td { padding: 12px; border: 1px solid #e5e7eb; text-align: left; }
        th { background: #3b82f6; color: white; }
        .btn { padding: 8px 12px; background: #10b981; color: white; text-decoration: none; border-radius: 5px; }
        .btn:hover { background: #059669; }
        .top-bar { margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        h2 { color: #1f2937; }
    </style>
</head>
<body>

    <div class="top-bar">
        <h2>Daftar Kendaraan</h2>
        <a href="{{ route('admin.kendaraan.create') }}" class="btn">+ Tambah Kendaraan</a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;padding:10px;color:#166534;border-radius:5px;margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Jenis</th>
                <th>Tipe</th>
                <th>Kapasitas</th>
                <th>Harga</th>
                <th>Fasilitas</th>
                <th>Gambar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kendaraans as $k)
                <tr>
                    <td>{{ $k->jenis }}</td>
                    <td>{{ $k->tipe }}</td>
                    <td>{{ $k->kapasitas }}</td>
                    <td>Rp{{ number_format($k->harga, 0, ',', '.') }}</td>
                    <td>{{ $k->fasilitas }}</td>
                    <td>
                        @if($k->gambar)
                            <img src="{{ asset('storage/' . $k->gambar) }}" alt="gambar" width="80">
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
