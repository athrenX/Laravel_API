<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kelola Lokasi</title>
    <style>
        body { font-family: sans-serif; background: #f9fafb; padding: 40px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
        h2 { margin-bottom: 20px; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 12px; text-align: left; }
        th { background: #f3f4f6; }
        a.button, button { padding: 8px 16px; text-decoration: none; background: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; }
        a.button:hover, button:hover { background: #2563eb; }
        .action-buttons { display: flex; gap: 10px; }
        .danger { background: #ef4444; }
        .danger:hover { background: #dc2626; }
        .add-btn { margin-bottom: 20px; display: inline-block; background: #10b981; }
        .add-btn:hover { background: #059669; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Kelola Lokasi</h2>

        <a href="{{ route('admin.lokasi.create') }}" class="button add-btn">+ Tambah Lokasi</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lokasis as $lokasi)
                <tr>
                    <td>{{ $lokasi->id }}</td>
                    <td>{{ $lokasi->name }}</td>
                    <td>{{ $lokasi->alamat }}</td>
                    <td>{{ $lokasi->latitude }}</td>
                    <td>{{ $lokasi->longitude }}</td>
                    <td class="action-buttons">
                        <a href="{{ route('admin.lokasi.edit', $lokasi->id) }}" class="button">Edit</a>
                        <form action="{{ route('admin.lokasi.destroy', $lokasi->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus lokasi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if($lokasis->isEmpty())
                <tr>
                    <td colspan="6" style="text-align: center;">Belum ada lokasi.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

</body>
</html>
