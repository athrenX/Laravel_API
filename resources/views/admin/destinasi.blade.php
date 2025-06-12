<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Destinasi - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
      --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    }
    
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .main-header {
      background: var(--primary-gradient);
      color: white;
      padding: 2rem 0;
      margin-bottom: 2rem;
      border-radius: 0 0 20px 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      backdrop-filter: blur(10px);
      background: rgba(255,255,255,0.95);
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .card-header {
      background: var(--primary-gradient) !important;
      color: white !important;
      border-radius: 15px 15px 0 0 !important;
      padding: 1.25rem 1.5rem;
      font-weight: 600;
      font-size: 1.1rem;
    }
    
    .form-control, .form-select {
      border: 2px solid #e9ecef;
      border-radius: 10px;
      padding: 0.75rem 1rem;
      transition: all 0.3s ease;
      background: rgba(255,255,255,0.8);
    }
    
    .form-control:focus, .form-select:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
      background: white;
    }
    
    .form-label {
      font-weight: 600;
      color: #495057;
      margin-bottom: 0.5rem;
    }
    
    .btn {
      border-radius: 10px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
      border: none;
    }
    
    .btn-success {
      background: var(--success-gradient);
      box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);
    }
    
    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(17, 153, 142, 0.6);
    }
    
    .btn-primary {
      background: var(--primary-gradient);
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
    }
    
    .btn-danger {
      background: var(--danger-gradient);
      box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
    }
    
    .btn-danger:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(255, 107, 107, 0.6);
    }
    
    .table {
      border-radius: 10px;
      overflow: hidden;
      background: white;
    }
    
    .table thead th {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border: none;
      font-weight: 600;
      padding: 1rem;
      color: #495057;
    }
    
    .table tbody td {
      padding: 1rem;
      vertical-align: middle;
      border-color: #f1f3f4;
    }
    
    .img-thumbnail {
      border-radius: 8px;
      transition: transform 0.3s ease;
    }
    
    .img-thumbnail:hover {
      transform: scale(1.1);
    }
    
    .alert {
      border: none;
      border-radius: 10px;
      padding: 1rem 1.5rem;
      margin-bottom: 1.5rem;
    }
    
    .alert-success {
      background: linear-gradient(135deg, rgba(17, 153, 142, 0.1) 0%, rgba(56, 239, 125, 0.1) 100%);
      border-left: 4px solid #11998e;
      color: #0f5132;
    }
    
    .alert-danger {
      background: linear-gradient(135deg, rgba(255, 107, 107, 0.1) 0%, rgba(238, 90, 36, 0.1) 100%);
      border-left: 4px solid #ff6b6b;
      color: #842029;
    }
    
    .input-group-text {
      background: var(--primary-gradient);
      color: white;
      border: none;
      border-radius: 10px 0 0 10px;
    }
    
    .gallery-preview {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 10px;
    }
    
    .price-display {
      font-weight: 700;
      color: #11998e;
      font-size: 1.1rem;
    }
    
    .loading-spinner {
      display: none;
    }
    
    .no-rating {
      color: #6c757d;
      font-style: italic;
      font-size: 0.875rem;
      background: #f8f9fa;
      padding: 0.25rem 0.5rem;
      border-radius: 15px;
      display: inline-block;
    }
    
    .rating-display {
      color: #ffc107;
    }
    
    .status-badge {
      font-size: 0.75rem;
      padding: 0.25rem 0.5rem;
      border-radius: 12px;
    }
    
    .status-new {
      background: linear-gradient(45deg, #28a745, #20c997);
      color: white;
    }
    
    @media (max-width: 768px) {
      .main-header {
        padding: 1.5rem 0;
      }
      
      .card-body {
        padding: 1rem;
      }
      
      .table-responsive {
        font-size: 0.875rem;
      }
      
      .btn {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
      }
    }
  </style>
</head>
<body>
  <div class="main-header">
    <div class="container">
      <div class="row align-items-center">
        <div class="col">
          <h1 class="mb-0"><i class="fas fa-map-marked-alt me-3"></i>Kelola Destinasi Wisata</h1>
          <p class="mb-0 opacity-75">Sistem Manajemen Destinasi Pariwisata</p>
        </div>
      </div>
    </div>
  </div>

  <div class="container pb-5">
    @if(session('success'))
      <div class="alert alert-success">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Terjadi kesalahan:</strong>
        <ul class="mb-0 mt-2">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- Form Tambah Destinasi -->
    <div class="card mb-5">
      <div class="card-header">
        <i class="fas fa-plus-circle me-2"></i>Tambah Destinasi Baru
      </div>
      <div class="card-body p-4">
        <form method="POST" action="/admin/destinasi" enctype="multipart/form-data" id="addDestinationForm">
          @csrf

          <div class="row mb-4">
            <div class="col-md-6 mb-3">
              <label class="form-label"><i class="fas fa-tag me-1"></i>Nama Destinasi</label>
              <input type="text" name="nama" class="form-control" placeholder="Masukkan nama destinasi" value="{{ old('nama') }}" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label"><i class="fas fa-list me-1"></i>Kategori</label>
              <select name="kategori" class="form-select" required>
                <option value="">Pilih Kategori</option>
                <option value="Gunung" {{ old('kategori') == 'Gunung' ? 'selected' : '' }}>🏔 Gunung</option>
                <option value="Pantai" {{ old('kategori') == 'Pantai' ? 'selected' : '' }}>🏖 Pantai</option>
                <option value="Danau" {{ old('kategori') == 'Danau' ? 'selected' : '' }}>🏞 Danau</option>
                <option value="Air Terjun" {{ old('kategori') == 'Air Terjun' ? 'selected' : '' }}>💦 Air Terjun</option>
                <option value="Taman Nasional" {{ old('kategori') == 'Taman Nasional' ? 'selected' : '' }}>🌳 Taman Nasional</option>
                <option value="Budaya" {{ old('kategori') == 'Budaya' ? 'selected' : '' }}>🏛 Budaya</option>
              </select>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label"><i class="fas fa-align-left me-1"></i>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="4" placeholder="Deskripsikan destinasi wisata ini..." required>{{ old('deskripsi') }}</textarea>
          </div>

          <div class="row mb-4">
            <div class="col-md-12 mb-3">
              <label class="form-label"><i class="fas fa-money-bill-wave me-1"></i>Harga (Rupiah)</label>
              <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" step="1000" name="harga" class="form-control" placeholder="50000" value="{{ old('harga') }}" required>
              </div>
              <small class="text-muted">Rating akan diberikan oleh pengunjung setelah mereka berkunjung</small>
            </div>
          </div>

          <div class="row mb-4">
            <div class="col-md-6 mb-3">
              <label class="form-label"><i class="fas fa-crosshairs me-1"></i>Latitude</label>
              <input type="text" name="lat" class="form-control" placeholder="-6.123456" value="{{ old('lat') }}" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label"><i class="fas fa-crosshairs me-1"></i>Longitude</label>
              <input type="text" name="lng" class="form-control" placeholder="106.123456" value="{{ old('lng') }}" required>
            </div>
          </div>

          <div class="row mb-4">
            <div class="col-md-6 mb-3">
              <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Lokasi</label>
              <input type="text" name="lokasi" class="form-control" placeholder="Kota, Provinsi" value="{{ old('lokasi') }}" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label"><i class="fas fa-image me-1"></i>Gambar Utama</label>
              <input type="file" name="gambar" class="form-control" required accept="image/*" id="mainImage">
              <small class="text-muted mt-1">Format: JPG, PNG, WebP (Max: 50MB)</small>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label"><i class="fas fa-images me-1"></i>Galeri Gambar</label>
            <input type="file" name="galeri[]" class="form-control" multiple accept="image/*" id="galleryImages">
            <small class="text-muted mt-1">Pilih beberapa gambar untuk galeri (Opsional, Max: 50MB per file)</small>
            <div class="gallery-preview" id="galleryPreview"></div>
          </div>

          <div class="d-flex gap-3">
            <button type="submit" class="btn btn-success">
              <i class="fas fa-save me-2"></i>Simpan Destinasi
              <span class="loading-spinner spinner-border spinner-border-sm ms-2" role="status"></span>
            </button>
            <button type="reset" class="btn btn-outline-secondary">
              <i class="fas fa-undo me-2"></i>Reset Form
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Tabel Daftar Destinasi -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <i class="fas fa-list me-2"></i>Daftar Destinasi Wisata
        </div>
        <div class="text-white-50">
          <small>Total: {{ count($destinasis) }} destinasi</small>
        </div>
      </div>
      <div class="card-body p-0">
        @if(count($destinasis) > 0)
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th><i class="fas fa-tag me-1"></i>Destinasi</th>
                  <th><i class="fas fa-list me-1"></i>Kategori</th>
                  <th><i class="fas fa-money-bill me-1"></i>Harga</th>
                  <th><i class="fas fa-star me-1"></i>Rating</th>
                  <th><i class="fas fa-map-marker me-1"></i>Lokasi</th>
                  <th><i class="fas fa-crosshairs me-1"></i>Koordinat</th>
                  <th><i class="fas fa-image me-1"></i>Gambar</th>
                  <th><i class="fas fa-images me-1"></i>Galeri</th>
                  <th><i class="fas fa-cogs me-1"></i>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($destinasis as $destinasi)
                  <tr>
                    <td>
                      <div class="fw-bold">{{ $destinasi->nama }}</div>
                      <div class="small text-muted">
                        <i class="fas fa-align-left me-1"></i>
                        {{ Str::limit($destinasi->deskripsi, 50) }}
                      </div>
                    </td>
                    <td>
                      <span class="badge bg-primary rounded-pill">
                        @switch($destinasi->kategori)
                          @case('Gunung')
                            🏔 {{ $destinasi->kategori }}
                            @break
                          @case('Pantai')
                            🏖 {{ $destinasi->kategori }}
                            @break
                          @case('Danau')
                            🏞 {{ $destinasi->kategori }}
                            @break
                          @case('Air Terjun')
                            💦 {{ $destinasi->kategori }}
                            @break
                          @case('Taman Nasional')
                            🌳 {{ $destinasi->kategori }}
                            @break
                          @case('Budaya')
                            🏛 {{ $destinasi->kategori }}
                            @break
                          @default
                            📍 {{ $destinasi->kategori }}
                        @endswitch
                      </span>
                    </td>
                    <td>
                      <span class="price-display">Rp {{ number_format($destinasi->harga, 0, ',', '.') }}</span>
                    </td>
                    <td>
                      @if($destinasi->rating && $destinasi->rating > 0)
                        <div class="rating-display">
                          @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($destinasi->rating))
                              <i class="fas fa-star"></i>
                            @elseif($i - 0.5 <= $destinasi->rating)
                              <i class="fas fa-star-half-alt"></i>
                            @else
                              <i class="far fa-star"></i>
                            @endif
                          @endfor
                          <div class="small text-muted mt-1">{{ number_format($destinasi->rating, 1) }}/5</div>
                        </div>
                      @else
                        <div class="no-rating">
                          <i class="fas fa-clock me-1"></i>Belum ada rating
                        </div>
                        <div class="status-badge status-new mt-1">
                          <i class="fas fa-sparkles me-1"></i>Baru
                        </div>
                      @endif
                    </td>
                    <td>
                      <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $destinasi->lokasi }}
                    </td>
                    <td>
                      <small class="text-muted">
                        <div><strong>Lat:</strong> {{ $destinasi->lat }}</div>
                        <div><strong>Lng:</strong> {{ $destinasi->lng }}</div>
                      </small>
                    </td>
                    <td>
                      @if($destinasi->gambar)
                        <a href="{{ asset($destinasi->gambar) }}" target="_blank" data-bs-toggle="tooltip" title="Lihat gambar penuh">
                          <img src="{{ asset($destinasi->gambar) }}" width="80" height="60" class="img-thumbnail" style="object-fit: cover;">
                        </a>
                      @else
                        <div class="text-muted text-center py-3">
                          <i class="fas fa-image-slash"></i><br>
                          <small>Tidak ada</small>
                        </div>
                      @endif
                    </td>
                    
                          
                        @php
                        $galeri = is_string($destinasi->galeri) ? json_decode($destinasi->galeri, true) : $destinasi->galeri;
                        $galeri = is_array($galeri) ? array_filter($galeri, fn($item) => is_string($item) && !empty($item)) : [];
                    @endphp
                    
                    <td>
                          
                        @php
                        $galeri = is_string($destinasi->galeri) ? json_decode($destinasi->galeri, true) : $destinasi->galeri;
                        $galeri = is_array($galeri) ? array_filter($galeri, fn($item) => is_string($item) && !empty($item)) : [];
                    @endphp
                      @if(count($galeri) > 0)
                        <div class="d-flex flex-wrap gap-1">
                          @foreach(array_slice($galeri, 0, 3) as $galeriItem)
                            <a href="{{ asset('storage/' . $galeriItem) }}" target="_blank" data-bs-toggle="tooltip" title="Lihat galeri">
                              <img src="{{ asset('storage/' . $galeriItem) }}" width="40" height="30" class="img-thumbnail" style="object-fit: cover;">
                            </a>
                          @endforeach
                          @if(count($galeri) > 3)
                            <div class="d-flex align-items-center justify-content-center bg-light rounded" style="width: 40px; height: 30px; font-size: 10px;">
                              +{{ count($galeri) - 3 }}
                            </div>
                          @endif
                        </div>
                      @else
                        <div class="text-muted text-center">Tidak ada galeri</div>
                      @endif
                    </td>  
        
                    <td>
                      <div class="d-flex gap-2">
                        <a href="/admin/destinasi/{{ $destinasi->id }}/edit" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Edit destinasi">
                          <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="/admin/destinasi/{{ $destinasi->id }}" class="d-inline">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('⚠ Yakin ingin menghapus destinasi {{ $destinasi->nama }}? Tindakan ini tidak dapat dibatalkan!')" data-bs-toggle="tooltip" title="Hapus destinasi">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center py-5">
            <i class="fas fa-map-marked-alt text-muted" style="font-size: 4rem;"></i>
            <h5 class="text-muted mt-3">Belum ada destinasi</h5>
            <p class="text-muted">Tambahkan destinasi wisata pertama menggunakan form di atas</p>
          </div>
        @endif
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // Form submission loading state
    document.getElementById('addDestinationForm').addEventListener('submit', function(e) {
      const submitBtn = this.querySelector('button[type="submit"]');
      const spinner = submitBtn.querySelector('.loading-spinner');
      
      // Validate required fields
      const requiredFields = this.querySelectorAll('[required]');
      let isValid = true;
      
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          isValid = false;
          field.classList.add('is-invalid');
        } else {
          field.classList.remove('is-invalid');
        }
      });
      
      if (!isValid) {
        e.preventDefault();
        alert('Mohon lengkapi semua field yang wajib diisi!');
        return;
      }
      
      submitBtn.disabled = true;
      spinner.style.display = 'inline-block';
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    });

    // Gallery preview
    document.getElementById('galleryImages').addEventListener('change', function(e) {
      const preview = document.getElementById('galleryPreview');
      preview.innerHTML = '';
      
      if (e.target.files && e.target.files.length > 0) {
        const fileCount = e.target.files.length;
        const countBadge = document.createElement('div');
        countBadge.className = 'badge bg-info mb-2';
        countBadge.innerHTML = <i class="fas fa-images me-1"></i>${fileCount} file dipilih;
        preview.appendChild(countBadge);
        
        Array.from(e.target.files).slice(0, 5).forEach((file, index) => {
          if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
              const img = document.createElement('img');
              img.src = e.target.result;
              img.className = 'img-thumbnail';
              img.style.width = '60px';
              img.style.height = '45px';
              img.style.objectFit = 'cover';
              img.title = file.name;
              preview.appendChild(img);
            }
            reader.readAsDataURL(file);
          }
        });
        
        if (fileCount > 5) {
          const moreBadge = document.createElement('div');
          moreBadge.className = 'text-muted small mt-2';
          moreBadge.innerHTML = <i class="fas fa-plus me-1"></i>+${fileCount - 5} file lainnya;
          preview.appendChild(moreBadge);
        }
      }
    });

    // Main image preview
    document.getElementById('mainImage').addEventListener('change', function(e) {
      if (e.target.files && e.target.files[0]) {
        const file = e.target.files[0];
        if (file.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = function(e) {
            // Remove existing preview
            const existingPreview = document.getElementById('mainImagePreview');
            if (existingPreview) {
              existingPreview.remove();
            }
            
            // Create new preview
            const preview = document.createElement('div');
            preview.id = 'mainImagePreview';
            preview.className = 'mt-2';
            preview.innerHTML = `
              <img src="${e.target.result}" class="img-thumbnail" style="width: 100px; height: 75px; object-fit: cover;">
              <small class="text-muted d-block mt-1">${file.name}</small>
            `;
            
            document.getElementById('mainImage').parentNode.appendChild(preview);
          }
          reader.readAsDataURL(file);
        }
      }
    });

    // Form reset handler
    document.querySelector('button[type="reset"]').addEventListener('click', function() {
      // Clear previews
      document.getElementById('galleryPreview').innerHTML = '';
      const mainPreview = document.getElementById('mainImagePreview');
      if (mainPreview) {
        mainPreview.remove();
      }
      
      // Remove validation classes
      document.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
      });
    });

    // Smooth hover effects for cards
    document.querySelectorAll('.card').forEach(card => {
      card.addEventListener('mouseenter', function() {
        if (!this.classList.contains('no-hover')) {
          this.style.transform = 'translateY(-5px)';
        }
      });
      
      card.addEventListener('mouseleave', function() {
        if (!this.classList.contains('no-hover')) {
          this.style.transform = 'translateY(0)';
        }
      });
    });

    // Add loading animation to action buttons
    document.querySelectorAll('form[method="POST"] button[type="submit"]').forEach(btn => {
      btn.addEventListener('click', function(e) {
        if (this.closest('form').querySelector('input[name="_method"][value="DELETE"]')) {
          // Don't add loading to delete buttons since they have confirmation
          return;
        }
        
        setTimeout(() => {
          this.disabled = true;
          this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }, 100);
      });
    });
  </script>
</body>
</html>