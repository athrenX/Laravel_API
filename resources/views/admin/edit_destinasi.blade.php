<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Destinasi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
      --secondary-gradient: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    }
    
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .main-header {
      background: var(--warning-gradient);
      color: white;
      padding: 2rem 0;
      margin-bottom: 2rem;
      border-radius: 0 0 20px 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 15px 35px rgba(0,0,0,0.1);
      backdrop-filter: blur(10px);
      background: rgba(255,255,255,0.95);
      transition: all 0.3s ease;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 25px 45px rgba(0,0,0,0.15);
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
      transform: translateY(-2px);
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
      position: relative;
      overflow: hidden;
    }
    
    .btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s;
    }
    
    .btn:hover::before {
      left: 100%;
    }
    
    .btn-primary {
      background: var(--primary-gradient);
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
    }
    
    .btn-secondary {
      background: var(--secondary-gradient);
      box-shadow: 0 4px 15px rgba(108, 117, 125, 0.4);
    }
    
    .btn-secondary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(108, 117, 125, 0.6);
    }
    
    .alert {
      border: none;
      border-radius: 10px;
      padding: 1rem 1.5rem;
      margin-bottom: 1.5rem;
      border-left: 4px solid;
    }
    
    .alert-danger {
      background: linear-gradient(135deg, rgba(255, 107, 107, 0.1) 0%, rgba(238, 90, 36, 0.1) 100%);
      border-left-color: #ff6b6b;
      color: #842029;
    }
    
    .img-thumbnail {
      border-radius: 10px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .img-thumbnail:hover {
      transform: scale(1.05) rotate(2deg);
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    
    .current-image-container {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      padding: 1rem;
      border-radius: 10px;
      margin-bottom: 1rem;
      text-align: center;
    }
    
    .gallery-container {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      padding: 1rem;
      border-radius: 10px;
      margin-bottom: 1rem;
    }
    
    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
      gap: 10px;
      margin-top: 10px;
    }
    
    .breadcrumb {
      background: rgba(255,255,255,0.8);
      border-radius: 10px;
      padding: 1rem;
      margin-bottom: 1.5rem;
      backdrop-filter: blur(10px);
    }
    
    .breadcrumb-item a {
      color: #667eea;
      text-decoration: none;
      transition: color 0.3s ease;
    }
    
    .breadcrumb-item a:hover {
      color: #764ba2;
    }
    
    .input-group-text {
      background: var(--primary-gradient);
      color: white;
      border: none;
      border-radius: 10px 0 0 10px;
    }
    
    .loading-spinner {
      display: none;
    }
    
    .preview-section {
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
      border-radius: 10px;
      padding: 1.5rem;
      margin-top: 1rem;
    }
    
    @media (max-width: 768px) {
      .main-header {
        padding: 1.5rem 0;
      }
      
      .card-body {
        padding: 1rem;
      }
      
      .btn {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
      }
      
      .gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
      }
    }
  </style>
</head>
<body>
  <div class="main-header">
    <div class="container">
      <div class="row align-items-center">
        <div class="col">
          <h1 class="mb-0"><i class="fas fa-edit me-3"></i>Edit Destinasi Wisata</h1>
          <p class="mb-0 opacity-75">Perbarui informasi destinasi wisata</p>
        </div>
      </div>
    </div>
  </div>

  <div class="container pb-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="/admin/destinasi"><i class="fas fa-home me-1"></i>Kelola Destinasi</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
          <i class="fas fa-edit me-1"></i>Edit Destinasi
        </li>
      </ol>
    </nav>

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

    <!-- Form Edit Destinasi -->
    <div class="card">
      <div class="card-body p-4">
        <form method="POST" action="/admin/destinasi/{{ $destinasi->id }}" enctype="multipart/form-data" id="editDestinationForm">
          @csrf
          @method('PUT')

          <div class="row mb-4">
            <div class="col-md-6 mb-3">
              <label class="form-label"><i class="fas fa-tag me-1"></i>Nama Destinasi</label>
              <input type="text" name="nama" class="form-control" value="{{ $destinasi->nama }}" placeholder="Masukkan nama destinasi" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label"><i class="fas fa-list me-1"></i>Kategori</label>
              <select name="kategori" class="form-select" required>
                <option value="">Pilih Kategori</option>
                <option value="Gunung" {{ $destinasi->kategori == 'Gunung' ? 'selected' : '' }}>🏔️ Gunung</option>
                <option value="Pantai" {{ $destinasi->kategori == 'Pantai' ? 'selected' : '' }}>🏖️ Pantai</option>
              </select>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label"><i class="fas fa-align-left me-1"></i>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="4" placeholder="Deskripsikan destinasi wisata ini..." required>{{ $destinasi->deskripsi }}</textarea>
          </div>

          <div class="row mb-4">
            <div class="col-md-6 mb-3">
              <label class="form-label"><i class="fas fa-money-bill-wave me-1"></i>Harga (Rupiah)</label>
              <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" step="0.01" name="harga" class="form-control" value="{{ $destinasi->harga }}" placeholder="0" required>
              </div>
            </div>
           
            <div class="col-md-6 mb-3">
              <label class="form-label"><i class="fas fa-crosshairs me-1"></i>Latitude</label>
              <input type="text" name="lat" class="form-control" value="{{ $destinasi->lat }}" placeholder="-6.123456" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label"><i class="fas fa-crosshairs me-1"></i>Longitude</label>
              <input type="text" name="lng" class="form-control" value="{{ $destinasi->lng }}" placeholder="106.123456" required>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Lokasi</label>
            <input type="text" name="lokasi" class="form-control" value="{{ $destinasi->lokasi }}" placeholder="Kota, Provinsi" required>
          </div>

          <!-- Current Main Image -->
          <div class="mb-4">
            <label class="form-label"><i class="fas fa-image me-1"></i>Gambar Utama</label>
            @if($destinasi->gambar)
              <div class="current-image-container">
                <h6 class="text-muted mb-3"><i class="fas fa-image me-1"></i>Gambar Saat Ini</h6>
                <img src="{{ asset('storage/' . $destinasi->gambar) }}" class="img-thumbnail" style="max-width: 200px; max-height: 150px; object-fit: cover;">
                <div class="mt-2">
                  <small class="text-success"><i class="fas fa-check-circle me-1"></i>Gambar sudah terupload</small>
                </div>
              </div>
            @endif
            <input type="file" name="gambar" class="form-control" accept="image/*" id="mainImageEdit">
            <div class="form-text">
              <i class="fas fa-info-circle me-1"></i>
              Biarkan kosong jika tidak ingin mengganti gambar. Format: JPG, PNG, WebP (Max: 2MB)
            </div>
            <div id="newMainImagePreview" class="preview-section" style="display: none;">
              <h6 class="text-primary mb-2"><i class="fas fa-eye me-1"></i>Preview Gambar Baru</h6>
              <img id="newMainImageImg" class="img-thumbnail" style="max-width: 200px; max-height: 150px; object-fit: cover;">
            </div>
          </div>

          <!-- Current Gallery -->
          <div class="mb-4">
            <label class="form-label"><i class="fas fa-images me-1"></i>Galeri Gambar</label>
            @php
                $galeri = is_string($destinasi->galeri)
                    ? json_decode($destinasi->galeri, true)
                    : $destinasi->galeri;

                $galeri = is_array($galeri)
                    ? array_filter($galeri, fn($g) => is_string($g) && !empty($g))
                    : [];
            @endphp

            @if(count($galeri) > 0)
            <div class="gallery-container">
                <h6 class="text-muted mb-3"><i class="fas fa-images me-1"></i>Galeri Saat Ini ({{ count($galeri) }} gambar)</h6>
                <div class="gallery-grid">
                @foreach($galeri as $galeriItem)
                    <div class="text-center">
                    <img src="{{ asset('storage/' . $galeriItem) }}" class="img-thumbnail" style="width: 80px; height: 60px; object-fit: cover;" data-bs-toggle="tooltip" title="Klik untuk melihat ukuran penuh" onclick="window.open('{{ asset('storage/' . $galeriItem) }}', '_blank')">
                    </div>
                @endforeach
                </div>
                <div class="mt-2">
                <small class="text-success"><i class="fas fa-check-circle me-1"></i>{{ count($galeri) }} gambar tersimpan</small>
                </div>
            </div>
            @else
            <div class="gallery-container text-center text-muted">
                <i class="fas fa-images fa-3x mb-2 opacity-50"></i>
                <p class="mb-0">Belum ada gambar di galeri</p>
            </div>
            @endif

            <input type="file" name="galeri[]" class="form-control" multiple accept="image/*" id="galleryImagesEdit">
            <div class="form-text">
              <i class="fas fa-info-circle me-1"></i>
              Pilih gambar baru untuk ditambahkan ke galeri (akan ditambahkan ke galeri yang sudah ada)
            </div>
            <div id="newGalleryPreview" class="preview-section" style="display: none;">
              <h6 class="text-primary mb-2"><i class="fas fa-eye me-1"></i>Preview Gambar Baru untuk Galeri</h6>
              <div class="gallery-grid" id="newGalleryGrid"></div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Update Destinasi
                <span class="loading-spinner spinner-border spinner-border-sm ms-2" role="status"></span>
              </button>
              <a href="/admin/destinasi" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
              </a>
            </div>
            <div class="text-muted small">
              <i class="fas fa-clock me-1"></i>Terakhir diubah: <span class="fw-bold">{{ date('d M Y H:i', strtotime($destinasi->updated_at ?? 'now')) }}</span>
            </div>
          </div>
        </form>
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
    document.getElementById('editDestinationForm').addEventListener('submit', function() {
      const submitBtn = this.querySelector('button[type="submit"]');
      const spinner = submitBtn.querySelector('.loading-spinner');
      submitBtn.disabled = true;
      spinner.style.display = 'inline-block';
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan... <span class="spinner-border spinner-border-sm ms-2"></span>';
    });

    // Main image preview
    document.getElementById('mainImageEdit').addEventListener('change', function(e) {
      const preview = document.getElementById('newMainImagePreview');
      const img = document.getElementById('newMainImageImg');
      
      if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          img.src = e.target.result;
          preview.style.display = 'block';
        }
        reader.readAsDataURL(e.target.files[0]);
      } else {
        preview.style.display = 'none';
      }
    });

    // Gallery preview
    document.getElementById('galleryImagesEdit').addEventListener('change', function(e) {
      const preview = document.getElementById('newGalleryPreview');
      const grid = document.getElementById('newGalleryGrid');
      grid.innerHTML = '';
      
      if (e.target.files && e.target.files.length > 0) {
        preview.style.display = 'block';
        
        Array.from(e.target.files).forEach(file => {
          if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
              const div = document.createElement('div');
              div.className = 'text-center';
              div.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="width: 80px; height: 60px; object-fit: cover;">`;
              grid.appendChild(div);
            }
            reader.readAsDataURL(file);
          }
        });
      } else {
        preview.style.display = 'none';
      }
    });

    // Enhanced form validation
    document.getElementById('editDestinationForm').addEventListener('input', function(e) {
      const target = e.target;
      
      // Price validation
      if (target.name === 'harga' && target.value < 0) {
        target.setCustomValidity('Harga tidak boleh negatif');
      } else if (target.name === 'rating' && (target.value < 1 || target.value > 5)) {
        target.setCustomValidity('Rating harus antara 1-5');
      } else {
        target.setCustomValidity('');
      }
    });

    // Smooth animations
    document.querySelectorAll('.form-control, .form-select').forEach(input => {
      input.addEventListener('focus', function() {
        this.parentElement.style.transform = 'scale(1.02)';
      });
      
      input.addEventListener('blur', function() {
        this.parentElement.style.transform = 'scale(1)';
      });
    });

    // Auto-save draft functionality (optional enhancement)
    let autoSaveTimeout;
    document.querySelectorAll('input, textarea, select').forEach(input => {
      input.addEventListener('input', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
          // Could implement auto-save to localStorage here
          console.log('Auto-saving draft...');
        }, 2000);
      });
    });
  </script>
</body>
</html>