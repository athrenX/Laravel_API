<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Aktivitas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input[type=text], select, input[type=file] {
            width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box;
        }
        button {
            margin-top: 20px;
            padding: 10px 16px;
            background-color: #2196F3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        a.back-link {
            display: inline-block;
            margin-top: 20px;
            color: #2196F3;
            text-decoration: none;
        }
        .current-image {
            margin-top: 10px;
        }
        .error { color: red; font-size: 14px; }
    </style>
</head>
<body>

<h1>Edit Aktivitas</h1>

@if($errors->any())
    <div class="error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>- {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.activities.update', $activity->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <label for="title">Judul Aktivitas</label>
    <input type="text" id="title" name="title" value="{{ old('title', $activity->title) }}" required />

    <label for="image">Gambar (jpg, png, max 2MB)</label>
    <input type="file" id="image" name="image" accept="image/*" />
    @if($activity->image)
        <div class="current-image">
            <strong>Gambar saat ini:</strong><br>
            <img src="{{ asset('storage/'.$activity->image) }}" alt="Gambar Aktivitas" style="width: 150px; height: auto; margin-top: 8px;">
        </div>
    @endif

    <label for="category">Kategori</label>
    <select id="category" name="category" required>
        <option value="">-- Pilih Kategori --</option>
        <option value="Gunung" {{ old('category', $activity->category) == 'Gunung' ? 'selected' : '' }}>Gunung</option>
        <option value="Pantai" {{ old('category', $activity->category) == 'Pantai' ? 'selected' : '' }}>Pantai</option>
        <option value="Budaya" {{ old('category', $activity->category) == 'Budaya' ? 'selected' : '' }}>Budaya</option>
        <option value="Alam" {{ old('category', $activity->category) == 'Alam' ? 'selected' : '' }}>Alam</option>
    </select>

    <button type="submit">Update Aktivitas</button>
</form>

<a href="{{ route('admin.activities.index') }}" class="back-link">← Kembali ke Daftar Aktivitas</a>

</body>
</html>
