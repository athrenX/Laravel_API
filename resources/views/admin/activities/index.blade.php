<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Daftar Aktivitas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        a.button {
            background-color: #4caf50;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
        }
        form { display: inline; }
        button.delete-button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h1>Daftar Aktivitas Populer</h1>

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<a href="{{ route('admin.activities.create') }}" class="button">Tambah Aktivitas</a>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Judul</th>
            <th>Gambar</th>
            <th>Kategori</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($activities as $index => $activity)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $activity->title }}</td>
            <td>
                @if($activity->image)
                    <img src="{{ asset('storage/'.$activity->image) }}" alt="Gambar" style="width: 80px; height: auto;" />
                @else
                    Tidak ada gambar
                @endif
            </td>
            <td>{{ $activity->category }}</td>
            <td>
                <a href="{{ route('admin.activities.edit', $activity->id) }}" class="button" style="background-color: #2196F3;">Edit</a>
                <form action="{{ route('admin.activities.destroy', $activity->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus aktivitas ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-button">Hapus</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5">Tidak ada data aktivitas.</td>
        </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
