<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kendaraan</title>
    <style>
        body { font-family: sans-serif; background: #f3f4f6; padding: 40px; }
        form { max-width: 500px; background: white; padding: 30px; border-radius: 10px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 5px; }
        button { margin-top: 20px; padding: 12px; width: 100%; background: #3b82f6; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; }
        button:hover { background: #2563eb; }
        .error { color: #dc2626; margin-top: 5px; font-size: 14px; }
        h2 { text-align: center; color: #1f2937; margin-bottom: 20px; }
    </style>
</head>
<body>

    <form action="{{ route('admin.kendaraan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <h2>Tambah Kendaraan</h2>

        <label for="destinasi_id">Destinasi</label>
        <select name="destinasi_id" id="destinasi_id">
            @foreach($destinasis as $destinasi)
                <option value="{{ $destinasi->id }}" {{ old('destinasi_id') == $destinasi->id ? 'selected' : '' }}>
                    {{ $destinasi->nama }} ({{ $destinasi->lokasi }})
                </option>
            @endforeach
        </select>
        @error('destinasi_id') <div class="error">{{ $message }}</div> @enderror

        <label>Jenis Kendaraan</label>
        <input type="text" name="jenis" value="{{ old('jenis') }}">
        @error('jenis') <div class="error">{{ $message }}</div> @enderror

        <label>Tipe</label>
        <input type="text" name="tipe" value="{{ old('tipe') }}">
        @error('tipe') <div class="error">{{ $message }}</div> @enderror

        <label>Kapasitas</label>
        <input type="number" name="kapasitas" value="{{ old('kapasitas') }}">
        @error('kapasitas') <div class="error">{{ $message }}</div> @enderror

        <label>Harga</label>
        <input type="number" name="harga" step="0.01" value="{{ old('harga') }}">
        @error('harga') <div class="error">{{ $message }}</div> @enderror

        <label>Fasilitas (opsional)</label>
        <textarea name="fasilitas">{{ old('fasilitas', 'AC, Audio') }}</textarea>

        <label>Gambar</label>
        <input type="file" name="gambar" accept="image/*">
        @error('gambar') <div class="error">{{ $message }}</div> @enderror

        <button type="submit">Simpan</button>
    </form>

</body>
</html>
