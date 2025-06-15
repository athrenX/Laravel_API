<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pemesanan</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .btn-secondary {
            background-color: #95a5a6;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #3498db;
            color: white;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #f39c12;
            color: white;
        }

        .status-menunggu {
            background-color: #e74c3c;
            color: white;
        }

        .status-dibayar {
            background-color: #2ecc71;
            color: white;
        }

        .status-diproses {
            background-color: #3498db;
            color: white;
        }

        .status-selesai {
            background-color: #9b59b6;
            color: white;
        }

        .status-dibatalkan {
            background-color: #95a5a6;
            color: white;
        }

        .action-btns {
            display: flex;
            gap: 5px;
        }

        .action-btn {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            text-decoration: none;
            color: white;
        }

        .edit-btn {
            background-color: #f39c12;
        }

        .view-btn {
            background-color: #3498db;
        }

        .cancel-btn {
            background-color: #e74c3c;
        }

        .img-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Daftar Pemesanan</h1>
            <div>
                <a href="{{ route('admin.home') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pelanggan</th>
                    <th>Destinasi</th>
                    <th>Kendaraan</th>
                    <th>Kursi</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $pemesanan)
                <tr>
                    <td>#{{ $pemesanan['id'] }}</td>
                    <td>
                        <strong>{{ $pemesanan['user']['nama'] }}</strong><br>
                        {{ $pemesanan['user']['email'] }}
                    </td>
                    <td>
                        <strong>{{ $pemesanan['destinasi']['nama'] }}</strong><br>
                        {{ $pemesanan['destinasi']['lokasi'] }}
                    </td>
                    <td>
                        {{ $pemesanan['kendaraan']['jenis'] }} ({{ $pemesanan['kendaraan']['tipe'] }})<br>
                        Kapasitas: {{ $pemesanan['kendaraan']['kapasitas'] }}
                    </td>
                    <td>
                        Kursi: {{ implode(', ', $pemesanan['selected_seats']) }}<br>
                        Peserta: {{ $pemesanan['jumlah_peserta'] }}
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($pemesanan['tanggal_pemesanan'])->format('d M Y H:i') }}<br>
                        @if($pemesanan['expired_at'])
                            Exp: {{ \Carbon\Carbon::parse($pemesanan['expired_at'])->format('H:i') }}
                        @endif
                    </td>
                    <td>
                        Rp{{ number_format($pemesanan['total_harga'], 0, ',', '.') }}
                    </td>
                    <td>
                        @php
                            $statusClass = '';
                            switch($pemesanan['status']) {
                                case 'pending':
                                    $statusClass = 'status-pending';
                                    break;
                                case 'menunggu pembayaran':
                                    $statusClass = 'status-menunggu';
                                    break;
                                case 'dibayar':
                                    $statusClass = 'status-dibayar';
                                    break;
                                case 'diproses':
                                    $statusClass = 'status-diproses';
                                    break;
                                case 'selesai':
                                    $statusClass = 'status-selesai';
                                    break;
                                case 'dibatalkan':
                                    $statusClass = 'status-dibatalkan';
                                    break;
                            }
                        @endphp
                        <span class="status {{ $statusClass }}">{{ $pemesanan['status'] }}</span>
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.pemesanan.edit', $pemesanan['id']) }}" class="action-btn edit-btn">Edit</a>
                            <a href="{{ route('admin.pemesanan.show', $pemesanan['id']) }}" class="action-btn view-btn">Lihat</a>
                            @if($pemesanan['status'] == 'menunggu pembayaran' || $pemesanan['status'] == 'pending')
                                <form action="{{ route('admin.pemesanan.cancel', $pemesanan['id']) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="action-btn cancel-btn" onclick="return confirm('Batalkan pemesanan ini?')">Batal</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
