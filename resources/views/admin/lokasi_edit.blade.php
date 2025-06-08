<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Lokasi</title>
    <style>
        body { font-family: sans-serif; background: #f3f4f6; padding: 40px; }
        form { max-width: 500px; background: white; padding: 30px; border-radius: 10px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, textarea { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 5px; }
        button { margin-top: 20px; padding: 12px; width: 100%; background: #3b82f6; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; }
        button:hover { background: #2563eb; }
        .error { color: #dc2626; font-size: 14px; margin-top: 5px; }
        h2 { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>

    <form action="{{ route('admin.lokasi.update', $lokasi->id) }}" method="POST">
        @csrf
        @method('PUT')
        <h2>Edit Lokasi</h2>

        <label>Nama Lokasi</label>
        <input type="text" name="name" value="{{ old('name', $lokasi->name) }}">
        @error('name') <div class="error">{{ $message }}</div> @enderror

        <label>Alamat</label>
        <textarea name="alamat">{{ old('alamat', $lokasi->alamat) }}</textarea>
        @error('alamat') <div class="error">{{ $message }}</div> @enderror

        <label>Latitude</label>
        <input type="text" name="latitude" value="{{ old('latitude', $lokasi->latitude) }}">
        @error('latitude') <div class="error">{{ $message }}</div> @enderror

        <label>Longitude</label>
        <input type="text" name="longitude" value="{{ old('longitude', $lokasi->longitude) }}">
        @error('longitude') <div class="error">{{ $message }}</div> @enderror

        <button type="submit">Perbarui</button>
    </form>

</body>
</html>
