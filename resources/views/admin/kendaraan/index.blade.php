<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kendaraan</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Inter font for better typography */
        body {
            font-family: "Inter", sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 p-8 antialiased">

    <div class="top-bar flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Daftar Kendaraan</h2>
        <div class="flex space-x-3">
            <!-- Kembali ke Dashboard Admin Button (already present and styled) -->
            <a href="{{ route('admin.home') }}" class="btn bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-md shadow-md transition duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-75">
                &larr; Kembali ke Dashboard
            </a>
            <a href="{{ route('admin.kendaraan.create') }}" class="btn bg-emerald-500 hover:bg-emerald-600 text-white py-2 px-4 rounded-md shadow-md transition duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-opacity-75">
                + Tambah Kendaraan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-md mb-6 shadow-sm" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-lg shadow-lg">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-3 px-4 bg-blue-600 text-white text-left text-sm font-semibold rounded-tl-lg">ID</th>
                    <th class="py-3 px-4 bg-blue-600 text-white text-left text-sm font-semibold">Destinasi</th>
                    <th class="py-3 px-4 bg-blue-600 text-white text-left text-sm font-semibold">Jenis</th>
                    <th class="py-3 px-4 bg-blue-600 text-white text-left text-sm font-semibold">Tipe</th>
                    <th class="py-3 px-4 bg-blue-600 text-white text-left text-sm font-semibold">Kapasitas</th>
                    <th class="py-3 px-4 bg-blue-600 text-white text-left text-sm font-semibold">Kursi Tersedia</th>
                    <th class="py-3 px-4 bg-blue-600 text-white text-left text-sm font-semibold">Harga</th>
                    <th class="py-3 px-4 bg-blue-600 text-white text-left text-sm font-semibold">Fasilitas</th>
                    <th class="py-3 px-4 bg-blue-600 text-white text-left text-sm font-semibold">Gambar</th>
                    <th class="py-3 px-4 bg-blue-600 text-white text-left text-sm font-semibold rounded-tr-lg">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($kendaraans as $k)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $k->id }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $k->destinasi->nama ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $k->jenis }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $k->tipe }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $k->kapasitas }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ implode(', ', $k->available_seats ?? []) }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">Rp{{ number_format($k->harga, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $k->fasilitas }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">
                            @if($k->gambar)
                                <img src="{{ asset('storage/' . $k->gambar) }}" alt="gambar" class="w-20 h-auto rounded-md shadow-sm object-cover">
                            @else
                                -
                            @endif
                        </td>
                        <td class="py-3 px-4 text-sm flex space-x-2">
                            <a href="{{ route('admin.kendaraan.edit', $k->id) }}" class="btn bg-amber-500 hover:bg-amber-600 text-white py-1 px-3 rounded-md shadow-sm transition duration-300 ease-in-out">Edit</a>
                            <form action="{{ route('admin.kendaraan.destroy', $k->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded-md shadow-sm transition duration-300 ease-in-out" onclick="return confirm('Apakah Anda yakin ingin menghapus kendaraan ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>
</html>
