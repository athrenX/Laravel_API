<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Dashboard Admin untuk Kelola Destinasi, Lokasi, Aktivitas, Kendaraan, dan Pemesanan" />
    <title>Dashboard Admin</title>
    <!-- Tailwind CSS CDN for consistency, though custom styles are still present -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif; /* Using Inter for consistency */
            background: #f3f4f6;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: #fff;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
            box-sizing: border-box;
        }
        h2 {
            margin-bottom: 30px;
            color: #1f2937;
            font-weight: 700;
            font-size: 28px;
        }
        .menu-button {
            display: block;
            width: 100%;
            background-color: #3b82f6;
            color: #fff;
            padding: 14px 0;
            margin: 10px 0;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            user-select: none;
        }
        .menu-button:hover,
        .menu-button:focus {
            background-color: #2563eb;
            outline: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Dashboard Admin</h2>

        <a href="{{ route('admin.destinasi.index') }}" class="menu-button">Kelola Destinasi</a>
        <a href="{{ route('admin.lokasi.index') }}" class="menu-button">Kelola Lokasi</a>
        <a href="{{ route('admin.activities.index') }}" class="menu-button">Kelola Aktivitas</a>
        <a href="{{ route('admin.kendaraan.index') }}" class="menu-button">Kelola Kendaraan</a>
        <!-- New button for managing orders -->
        <a href="{{ route('admin.pemesanan.index') }}" class="menu-button">Kelola Pemesanan</a>
    </div>
</body>
</html>
