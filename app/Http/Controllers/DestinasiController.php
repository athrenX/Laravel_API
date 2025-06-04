<?php

namespace App\Http\Controllers;

use App\Models\Destinasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Exception;

class DestinasiController extends Controller
{
    public function index()
    {
        try {
            $destinasis = Destinasi::all();
            return response()->json([
                'success' => true,
                'data' => $destinasis,
                'message' => 'Data destinasi berhasil diambil'
            ]);
        } catch (Exception $e) {
            Log::error('Gagal mengambil data destinasi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data destinasi',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan server'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            // Validasi input
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'kategori' => 'required|string|max:100',
                'deskripsi' => 'required|string',
                'harga' => 'required|numeric|min:0',
                'rating' => 'nullable|numeric|min:0|max:5',
                'lat' => 'required|numeric|between:-90,90',
                'lng' => 'required|numeric|between:-180,180',
                'lokasi' => 'required|string|max:255',
                'gambar' => 'required|file|image|mimes:jpeg,png,jpg,gif|max:51200',
                'galeri.*' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:51200',
            ]);

            // Upload gambar utama
            if (!$request->hasFile('gambar')) {
                throw new Exception('File gambar utama tidak ditemukan');
            }

            $gambarFile = $request->file('gambar');
            if (!$gambarFile->isValid()) {
                throw new Exception('File gambar utama tidak valid atau rusak');
            }

            $gambarName = time() . '_' . Str::slug(pathinfo($gambarFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $gambarFile->getClientOriginalExtension();
            $gambarPath = $gambarFile->storeAs('gambar_destinasi', $gambarName, 'public');
            if (!$gambarPath) {
                throw new Exception('Gagal mengupload gambar utama');
            }

            // Upload galeri
            $galeriPaths = [];
            if ($request->hasFile('galeri')) {
                foreach ($request->file('galeri') as $index => $file) {
                    if (!$file->isValid()) continue;
                
                    $galeriName = time() . "_galeri_{$index}_" . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                    $galeriPath = $file->storeAs('galeri_destinasi', $galeriName, 'public');
                    if ($galeriPath) $galeriPaths[] = $galeriPath;
                }
                
            }

            // Simpan ke database
            $destinasi = Destinasi::create([
                'nama' => $validated['nama'],
                'kategori' => $validated['kategori'],
                'deskripsi' => $validated['deskripsi'],
                'harga' => $validated['harga'],
                'rating' => $validated['rating'] ?? null,
                'lat' => $validated['lat'],
                'lng' => $validated['lng'],
                'lokasi' => $validated['lokasi'],
                'gambar' => $gambarPath,
                'galeri' => $galeriPaths,
            ]);

            if (!$destinasi) {
                throw new Exception('Gagal menyimpan data destinasi ke database');
            }

            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $destinasi,
                    'message' => 'Destinasi berhasil ditambahkan'
                ], 201);
            }

            return redirect()->route('admin.destinasi.index')
                ->with('success', 'Destinasi berhasil ditambahkan');

        } catch (ValidationException $e) {
            DB::rollBack();
            
            // Hapus file yang sudah terupload jika validasi gagal
            if (isset($gambarPath) && Storage::disk('public')->exists($gambarPath)) {
                Storage::disk('public')->delete($gambarPath);
            }
            if (!empty($galeriPaths)) {
                foreach ($galeriPaths as $path) {
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }

            Log::warning('Validasi gagal saat menyimpan destinasi', [
                'errors' => $e->errors(),
                'input' => $request->except(['gambar', 'galeri'])
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput($request->except(['gambar', 'galeri']));

        } catch (Exception $e) {
            DB::rollBack();
            
            // Hapus file yang sudah terupload jika terjadi error
            if (isset($gambarPath) && Storage::disk('public')->exists($gambarPath)) {
                Storage::disk('public')->delete($gambarPath);
            }
            if (!empty($galeriPaths)) {
                foreach ($galeriPaths as $path) {
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }

            Log::error('Gagal menyimpan destinasi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['gambar', 'galeri'])
            ]);

            $errorMessage = config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan saat menyimpan destinasi';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput($request->except(['gambar', 'galeri']));
        }
    }

    public function show($id)
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID destinasi tidak valid'
                ], 400);
            }

            $destinasi = Destinasi::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $destinasi,
                'message' => 'Data destinasi berhasil diambil'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("Destinasi dengan ID {$id} tidak ditemukan");
            
            return response()->json([
                'success' => false,
                'message' => 'Destinasi tidak ditemukan'
            ], 404);

        } catch (Exception $e) {
            Log::error('Gagal mengambil data destinasi: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data destinasi',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan server'
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return redirect()->route('admin.destinasi.index')
                    ->with('error', 'ID destinasi tidak valid');
            }

            $destinasi = Destinasi::findOrFail($id);
            return view('admin.edit_destinasi', compact('destinasi'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("Destinasi dengan ID {$id} tidak ditemukan untuk edit");
            
            return redirect()->route('admin.destinasi.index')
                ->with('error', 'Destinasi tidak ditemukan');

        } catch (Exception $e) {
            Log::error('Gagal mengambil data destinasi untuk edit: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.destinasi.index')
                ->with('error', 'Terjadi kesalahan saat mengambil data destinasi');
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            if (!is_numeric($id) || $id <= 0) {
                throw new Exception('ID destinasi tidak valid');
            }

            $destinasi = Destinasi::findOrFail($id);
            $oldGambar = $destinasi->gambar;
            $oldGaleri = $destinasi->galeri;

            // Validasi input
            $validated = $request->validate([
                'nama' => 'sometimes|required|string|max:255',
                'kategori' => 'sometimes|required|string|max:100',
                'deskripsi' => 'sometimes|required|string',
                'harga' => 'sometimes|required|numeric|min:0',
                'rating' => 'nullable|numeric|min:0|max:5',
                'lat' => 'sometimes|required|numeric|between:-90,90',
                'lng' => 'sometimes|required|numeric|between:-180,180',
                'lokasi' => 'sometimes|required|string|max:255',
                'gambar' => 'sometimes|file|image|mimes:jpeg,png,jpg,gif|max:2048',
                'galeri.*' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Update gambar utama jika ada
            if ($request->hasFile('gambar')) {
                $gambarFile = $request->file('gambar');
                if (!$gambarFile->isValid()) {
                    throw new Exception('File gambar utama tidak valid atau rusak');
                }

                $gambarName = time() . '_' . Str::slug(pathinfo($gambarFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $gambarFile->getClientOriginalExtension();
                $newGambarPath = $gambarFile->storeAs('gambar_destinasi', $gambarName, 'public');

                if (!$newGambarPath) {
                    throw new Exception('Gagal mengupload gambar utama baru');
                }
                
                $validated['gambar'] = $newGambarPath;
            }
            // Pastikan oldGaleri adalah array valid
            if (is_string($oldGaleri)) {
                $decoded = json_decode($oldGaleri, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $oldGaleri = $decoded;
                } else {
                    $oldGaleri = []; // fallback jika rusak
                }
            }

            // Update galeri jika ada
           // Update galeri jika ada
            if ($request->hasFile('galeri')) {
                $galeriPaths = [];
                foreach ($request->file('galeri') as $index => $file) {
                    if (!$file->isValid()) continue;

                    $galeriName = time() . "_galeri_{$index}_" . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                    $galeriPath = $file->storeAs('galeri_destinasi', $galeriName, 'public');
                    if ($galeriPath) $galeriPaths[] = $galeriPath;
                }

                // Tambahkan ke data update
                $validated['galeri'] = $galeriPaths;

                // Hapus file galeri lama jika ada
                if (!empty($oldGaleri)) {
                    foreach ($oldGaleri as $gambar) {
                        if (is_string($gambar) && Storage::disk('public')->exists($gambar)) {
                            Storage::disk('public')->delete($gambar);
                        }
                    }
                }
            }


            // Update data
            $updated = $destinasi->update($validated);
            if (!$updated) {
                throw new Exception('Gagal memperbarui data destinasi');
            }

            
           
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $destinasi->fresh(),
                    'message' => 'Destinasi berhasil diperbarui'
                ]);
            }

            return redirect()->route('admin.destinasi.index')
                ->with('success', 'Destinasi berhasil diperbarui');

        } catch (ValidationException $e) {
            DB::rollBack();
            
            // Hapus file baru yang sudah terupload jika validasi gagal
            if (isset($validated['gambar']) && Storage::disk('public')->exists($validated['gambar'])) {
                Storage::disk('public')->delete($validated['gambar']);
            }
            if (isset($galeriPaths) && !empty($oldGaleri)) {
                foreach ($oldGaleri as $gambar) {
                    if (is_string($gambar) && Storage::disk('public')->exists($gambar)) {
                        Storage::disk('public')->delete($gambar);
                    }
                }
            
                // Simpan galeri baru ke field galeri
                $validated['galeri'] = $galeriPaths;
            }
            

            Log::warning('Validasi gagal saat memperbarui destinasi', [
                'id' => $id,
                'errors' => $e->errors(),
                'input' => $request->except(['gambar', 'galeri'])
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput($request->except(['gambar', 'galeri']));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            
            Log::warning("Destinasi dengan ID {$id} tidak ditemukan untuk update");

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Destinasi tidak ditemukan'
                ], 404);
            }

            return redirect()->route('admin.destinasi.index')
                ->with('error', 'Destinasi tidak ditemukan');

        } catch (Exception $e) {
            DB::rollBack();
            
            // Hapus file baru yang sudah terupload jika terjadi error
            if (isset($validated['gambar']) && Storage::disk('public')->exists($validated['gambar'])) {
                Storage::disk('public')->delete($validated['gambar']);
            }
            if (isset($validated['galeri'])) {
                foreach ($validated['galeri'] as $path) {
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }

            Log::error('Gagal memperbarui destinasi: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['gambar', 'galeri'])
            ]);

            $errorMessage = config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan saat memperbarui destinasi';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput($request->except(['gambar', 'galeri']));
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            if (!is_numeric($id) || $id <= 0) {
                throw new Exception('ID destinasi tidak valid');
            }

            $destinasi = Destinasi::findOrFail($id);
            $gambarPath = $destinasi->gambar;
            $galeriPaths = $destinasi->galeri;

            // Hapus dari database
            $deleted = $destinasi->delete();
            if (!$deleted) {
                throw new Exception('Gagal menghapus data destinasi dari database');
            }

            // Hapus file gambar utama
            if ($gambarPath && Storage::disk('public')->exists($gambarPath)) {
                $deleteGambar = Storage::disk('public')->delete($gambarPath);
                if (!$deleteGambar) {
                    Log::warning("Gagal menghapus file gambar: {$gambarPath}");
                }
            }

            // Hapus file galeri
            if ($galeriPaths && is_array($galeriPaths)) {
                foreach ($galeriPaths as $gambar) {
                    if (Storage::disk('public')->exists($gambar)) {
                        $deleteGaleri = Storage::disk('public')->delete($gambar);
                        if (!$deleteGaleri) {
                            Log::warning("Gagal menghapus file galeri: {$gambar}");
                        }
                    }
                }
            }

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Destinasi berhasil dihapus'
                ]);
            }

            return redirect()->route('admin.destinasi.index')
                ->with('success', 'Destinasi berhasil dihapus');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            
            Log::warning("Destinasi dengan ID {$id} tidak ditemukan untuk dihapus");

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Destinasi tidak ditemukan'
                ], 404);
            }

            return redirect()->route('admin.destinasi.index')
                ->with('error', 'Destinasi tidak ditemukan');

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Gagal menghapus destinasi: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            $errorMessage = config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan saat menghapus destinasi';

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->route('admin.destinasi.index')
                ->with('error', $errorMessage);
        }
    }

    public function adminIndex()
    {
        try {
            $destinasis = Destinasi::all();
            return view('admin.destinasi', compact('destinasis'));

        } catch (Exception $e) {
            Log::error('Gagal mengambil data destinasi untuk admin: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('admin.destinasi', ['destinasis' => collect()])
                ->with('error', 'Terjadi kesalahan saat mengambil data destinasi');
        }
    }
}