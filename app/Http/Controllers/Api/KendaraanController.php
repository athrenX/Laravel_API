<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\Destinasi; // Import Destinasi model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB; // Import DB facade for transaction

class KendaraanController extends Controller
{
    /**
     * Display a listing of the vehicles for a specific destination.
     * Digunakan oleh Flutter untuk mendapatkan kendaraan berdasarkan destinasi.
     */
    public function indexByDestinasi($destinasiId)
    {
        try {
            $destinasi = Destinasi::findOrFail($destinasiId);
            $kendaraans = $destinasi->kendaraans; // Ambil kendaraan yang terkait dengan destinasi

            // Format URL gambar agar dapat diakses dari Flutter
            $kendaraans->each(function ($kendaraan) {
                if ($kendaraan->gambar) {
                    $kendaraan->gambar = asset('storage/' . $kendaraan->gambar);
                }
                // Pastikan available_seats selalu array, meskipun null dari DB
                // Laravel akan otomatis mengonversi kolom JSON ke array/object ketika diakses
                // Tapi untuk jaminan, kita bisa pastikan di sini jika ada keraguan pada versi Laravel lama
                $kendaraan->available_seats = is_array($kendaraan->available_seats) ? $kendaraan->available_seats : (json_decode($kendaraan->available_seats, true) ?? []);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Kendaraan retrieved successfully for destination.',
                'data' => $kendaraans,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Destinasi not found.',
                'data' => null,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve kendaraan: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Update the available seats for a specific vehicle.
     * Digunakan oleh Flutter setelah user memilih kursi.
     */
    public function updateSeats(Request $request, $kendaraanId)
    {
        DB::beginTransaction(); // Mulai transaksi database

        try {
            $request->validate([
                'booked_seats' => 'required|array',
                'booked_seats.*' => 'integer|min:1',
            ]);

            $kendaraan = Kendaraan::findOrFail($kendaraanId);

            // Pastikan available_seats selalu array
            $currentAvailableSeats = $kendaraan->available_seats ?? [];
            if (!is_array($currentAvailableSeats)) {
                $currentAvailableSeats = json_decode($currentAvailableSeats, true) ?? [];
            }

            $seatsToBook = $request->input('booked_seats');

            // Memastikan semua kursi yang akan dipesan benar-benar tersedia
            foreach ($seatsToBook as $seat) {
                if (!in_array($seat, $currentAvailableSeats)) {
                    DB::rollBack(); // Batalkan transaksi
                    return response()->json([
                        'status' => 'error',
                        'message' => "Kursi $seat tidak tersedia. Silakan refresh dan coba lagi.",
                        'data' => $kendaraan->fresh()->toArray(), // Kirim data kendaraan terbaru
                    ], 409); // Conflict status code
                }
            }

            // Hapus kursi yang dipesan dari daftar available_seats
            $newAvailableSeats = array_values(array_diff($currentAvailableSeats, $seatsToBook));
            $kendaraan->available_seats = $newAvailableSeats;
            $kendaraan->save(); // Simpan perubahan

            DB::commit(); // Commit transaksi

            // Format URL gambar agar dapat diakses dari Flutter setelah update
            // Gunakan $kendaraan->fresh() untuk mendapatkan data terbaru setelah save
            $updatedKendaraan = $kendaraan->fresh();
            if ($updatedKendaraan->gambar) {
                $updatedKendaraan->gambar = asset('storage/' . $updatedKendaraan->gambar);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Kursi berhasil dipesan.',
                'data' => $updatedKendaraan, // Mengembalikan data kendaraan yang diperbarui
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Kesalahan validasi',
                'errors' => $e->errors(),
                'data' => null,
            ], 422); // Unprocessable Entity
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Kendaraan tidak ditemukan.',
                'data' => null,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memesan kursi: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    // --- Bagian Admin untuk Kendaraan (CRUD) ---
    /**
     * Display a listing of all vehicles (for admin).
     */
    public function indexAdmin()
    {
        $kendaraans = Kendaraan::with('destinasi')->get(); // eager load destinasi
        return view('admin.kendaraan.index', compact('kendaraans'));
    }

    /**
     * Show the form for creating a new vehicle (for admin).
     */
    public function createAdmin()
    {
        $destinasis = Destinasi::all(); // Fetch all destinations to link
        return view('admin.kendaraan.create', compact('destinasis'));
    }

    /**
     * Store a newly created vehicle in storage (for admin).
     */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'destinasi_id' => 'required|exists:destinasis,id',
            'jenis' => 'required|string|max:255',
            'tipe' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
            'fasilitas' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('gambar')) {
            $imagePath = $request->file('gambar')->store('kendaraan_images', 'public');
        }

        Kendaraan::create([
            'destinasi_id' => $request->destinasi_id,
            'jenis' => $request->jenis,
            'tipe' => $request->tipe,
            'kapasitas' => $request->kapasitas,
            'harga' => $request->harga,
            'fasilitas' => $request->fasilitas,
            'gambar' => $imagePath,
            // Inisialisasi semua kursi sebagai tersedia, pastikan dimulai dari 1
            'available_seats' => array_values(range(1, $request->kapasitas)),
        ]);

        return redirect()->route('admin.kendaraan.index')->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified vehicle (for admin).
     */
    public function editAdmin(Kendaraan $kendaraan)
    {
        $destinasis = Destinasi::all();
        return view('admin.kendaraan.edit', compact('kendaraan', 'destinasis'));
    }

    /**
     * Update the specified vehicle in storage (for admin).
     */
    public function updateAdmin(Request $request, Kendaraan $kendaraan)
    {
        $request->validate([
            'destinasi_id' => 'required|exists:destinasis,id',
            'jenis' => 'required|string|max:255',
            'tipe' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
            'fasilitas' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imagePath = $kendaraan->gambar;
        if ($request->hasFile('gambar')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('gambar')->store('kendaraan_images', 'public');
        }

        $currentAvailableSeats = $kendaraan->available_seats ?? [];
        if (!is_array($currentAvailableSeats)) {
            $currentAvailableSeats = json_decode($currentAvailableSeats, true) ?? [];
        }

        // Jika kapasitas berubah, kita perlu menginisialisasi ulang available_seats
        if ($request->kapasitas != $kendaraan->kapasitas) {
            $newCapacity = $request->kapasitas;
            $newAvailableSeats = [];
            // Pertahankan kursi yang sudah ada dan masih valid
            foreach ($currentAvailableSeats as $seat) {
                if ($seat <= $newCapacity) {
                    $newAvailableSeats[] = $seat;
                }
            }
            // Tambahkan kursi baru jika kapasitas bertambah
            for ($i = $kendaraan->kapasitas + 1; $i <= $newCapacity; $i++) {
                $newAvailableSeats[] = $i;
            }
            sort($newAvailableSeats); // Urutkan kembali kursi
            $availableSeatsToSave = $newAvailableSeats;
        } else {
            $availableSeatsToSave = $currentAvailableSeats;
        }

        $kendaraan->update([
            'destinasi_id' => $request->destinasi_id,
            'jenis' => $request->jenis,
            'tipe' => $request->tipe,
            'kapasitas' => $request->kapasitas,
            'harga' => $request->harga,
            'fasilitas' => $request->fasilitas,
            'gambar' => $imagePath,
            'available_seats' => $availableSeatsToSave, // Simpan array yang diperbarui
        ]);

        return redirect()->route('admin.kendaraan.index')->with('success', 'Kendaraan berhasil diperbarui.');
    }

    /**
     * Remove the specified vehicle from storage (for admin).
     */
    public function destroyAdmin(Kendaraan $kendaraan)
    {
        if ($kendaraan->gambar && Storage::disk('public')->exists($kendaraan->gambar)) {
            Storage::disk('public')->delete($kendaraan->gambar);
        }
        $kendaraan->delete();
        return redirect()->route('admin.kendaraan.index')->with('success', 'Kendaraan berhasil dihapus.');
    }
}
