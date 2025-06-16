<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Review User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f6f8fa; }
        .rounded-circle { object-fit: cover; }
        .table th, .table td { vertical-align: middle !important; }
        .section-title { margin-top: 40px; margin-bottom: 16px; color: #133356; }
    </style>
</head>
<body>
<div class="container py-4">
    <h2 class="mb-3">Kelola Review User</h2>
    <a href="{{ route('admin.home') }}" class="btn btn-outline-primary mb-3">
        &larr; Kembali ke Home
    </a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        // Daftar destinasi utama
        $destinasiList = [
            'Gunung Bromo',
            'Gunung Sindoro',
            'Kawah Ijen',
            'Karimun Jawa',
            'Pantai Anyer',
            'Pantai Pangandaran'
        ];
    @endphp

    @foreach($destinasiList as $destinasiNama)
        @php
            $destinasiReviews = $reviews->filter(function($review) use ($destinasiNama) {
                return isset($review->destinasi) && $review->destinasi->nama === $destinasiNama;
            });
        @endphp
        <h4 class="section-title">{{ $destinasiNama }}</h4>
        <table class="table table-bordered table-striped mb-5">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Rating</th>
                    <th>Komentar</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($destinasiReviews as $review)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($review->user && $review->user->foto_profil)
                                <img src="{{ asset('storage/'.$review->user->foto_profil) }}"
                                     alt="Foto Profil" width="40" height="40" class="rounded-circle me-2">
                            @else
                                <span class="badge bg-secondary me-2" style="font-size:1.2em;">
                                    {{ strtoupper(substr($review->user->nama ?? 'U', 0, 1)) }}
                                </span>
                            @endif
                            {{ $review->user->nama ?? '-' }}
                        </div>
                    </td>
                    <td>
                        @for($i = 0; $i < 5; $i++)
                            @if($i < $review->rating)
                                <span class="text-warning">&#9733;</span>
                            @else
                                <span class="text-secondary">&#9734;</span>
                            @endif
                        @endfor
                    </td>
                    <td>{{ $review->comment }}</td>
                    <td>{{ \Carbon\Carbon::parse($review->created_at)->format('d M Y H:i') }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}"
                              onsubmit="return confirm('Yakin ingin hapus review ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada review untuk destinasi ini.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    @endforeach

    <!-- Pagination untuk semua review (opsional, bisa dihilangkan kalau tidak relevan per section) -->
    {{ $reviews->links('pagination::bootstrap-5') }}

</div>
</body>
</html>
