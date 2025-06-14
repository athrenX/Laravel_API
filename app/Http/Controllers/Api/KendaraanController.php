<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\Destinasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KendaraanController extends Controller
{
    public function indexByDestinasi($destinasiId)
    {
        try {
            $destinasi = Destinasi::findOrFail($destinasiId);
            $kendaraans = $destinasi->kendaraans;

            $kendaraans->each(function ($kendaraan) {
                if ($kendaraan->gambar) {
                    $kendaraan->gambar = asset('storage/' . $kendaraan->gambar);
                }
                // Pastikan available_seats dan held_seats selalu array of integers
                $kendaraan->available_seats = collect($kendaraan->available_seats ?? [])->map(function ($item) {
                    return (int) $item; // Pastikan semua elemen adalah integer
                })->toArray();
                $kendaraan->held_seats = collect($kendaraan->held_seats ?? [])->map(function ($item) {
                    return (int) $item; // Pastikan semua elemen adalah integer
                })->toArray();
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
            Log::error("Error in indexByDestinasi: " . $e->getMessage() . " on line " . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve kendaraan: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function holdSeats(Request $request, $kendaraanId)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'seats_to_hold' => 'required|array',
                'seats_to_hold.*' => 'integer|min:1',
            ]);

            $kendaraan = Kendaraan::where('id', $kendaraanId)->lockForUpdate()->firstOrFail();

            $currentAvailableSeats = collect($kendaraan->available_seats ?? [])->map(fn($item) => (int) $item)->toArray();
            $currentHeldSeats = collect($kendaraan->held_seats ?? [])->map(fn($item) => (int) $item)->toArray();
            $seatsToHold = collect($request->input('seats_to_hold'))->map(fn($item) => (int) $item)->toArray();

            Log::info("DEBUG_KENDARAAN_HOLD: holdSeats for Kendaraan ID: $kendaraanId");
            Log::info("DEBUG_KENDARAAN_HOLD: Current Available Seats (LOCKED): " . json_encode($currentAvailableSeats));
            Log::info("DEBUG_KENDARAAN_HOLD: Current Held Seats (LOCKED): " . json_encode($currentHeldSeats));
            Log::info("DEBUG_KENDARAAN_HOLD: Seats to Hold from Request: " . json_encode($seatsToHold));

            foreach ($seatsToHold as $seat) {
                if (!in_array($seat, $currentAvailableSeats)) {
                    DB::rollBack();
                    Log::warning("DEBUG_KENDARAAN_HOLD: Kursi $seat TIDAK TERSEDIA di available_seats. Current available: " . json_encode($currentAvailableSeats));
                    return response()->json([
                        'status' => 'error',
                        'message' => "Kursi $seat tidak tersedia lagi. Silakan refresh dan pilih kursi lain.",
                        'data' => $kendaraan->fresh()->toArray(),
                    ], 409);
                }
                if (in_array($seat, $currentHeldSeats)) {
                    DB::rollBack();
                    Log::warning("DEBUG_KENDARAAN_HOLD: Kursi $seat SUDAH DITAHAN. Current held: " . json_encode($currentHeldSeats));
                    return response()->json([
                        'status' => 'error',
                        'message' => "Kursi $seat sudah ditahan oleh pemesanan lain. Silakan refresh dan pilih kursi lain.",
                        'data' => $kendaraan->fresh()->toArray(),
                    ], 409);
                }
            }

            // Pindahkan kursi dari available ke held
            $newAvailableSeats = array_values(array_diff($currentAvailableSeats, $seatsToHold));
            $newHeldSeats = array_unique(array_merge($currentHeldSeats, $seatsToHold));
            sort($newAvailableSeats);
            sort($newHeldSeats);

            $kendaraan->available_seats = $newAvailableSeats;
            $kendaraan->held_seats = $newHeldSeats;
            $kendaraan->save(); // Simpan perubahan di database

            DB::commit();
            Log::info("DEBUG_KENDARAAN_HOLD: Kursi berhasil ditahan. New Available Seats: " . json_encode($newAvailableSeats) . ", New Held Seats: " . json_encode($newHeldSeats));

            $updatedKendaraan = $kendaraan->fresh(); // Ambil data terbaru setelah disimpan
            if ($updatedKendaraan->gambar) {
                $updatedKendaraan->gambar = asset('storage/' . $updatedKendaraan->gambar);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Kursi berhasil ditahan.',
                'data' => $updatedKendaraan,
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error("DEBUG_KENDARAAN_HOLD: Validation Error: " . json_encode($e->errors()));
            return response()->json([
                'status' => 'error',
                'message' => 'Kesalahan validasi',
                'errors' => $e->errors(),
                'data' => null,
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error("DEBUG_KENDARAAN_HOLD: ModelNotFoundException: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Kendaraan tidak ditemukan.',
                'data' => null,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("DEBUG_KENDARAAN_HOLD: General Error: " . $e->getMessage() . " on line " . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menahan kursi: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function releaseHeldSeats(Request $request, $kendaraanId)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'seats_to_release' => 'required|array',
                'seats_to_release.*' => 'integer|min:1',
            ]);

            $kendaraan = Kendaraan::where('id', $kendaraanId)->lockForUpdate()->firstOrFail();

            $currentAvailableSeats = collect($kendaraan->available_seats ?? [])->map(fn($item) => (int) $item)->toArray();
            $currentHeldSeats = collect($kendaraan->held_seats ?? [])->map(fn($item) => (int) $item)->toArray();
            $seatsToRelease = collect($request->input('seats_to_release'))->map(fn($item) => (int) $item)->toArray();

            Log::info("DEBUG_KENDARAAN_RELEASE: releaseHeldSeats for Kendaraan ID: $kendaraanId");
            Log::info("DEBUG_KENDARAAN_RELEASE: Current Held Seats (LOCKED): " . json_encode($currentHeldSeats));
            Log::info("DEBUG_KENDARAAN_RELEASE: Seats to Release from Request: " . json_encode($seatsToRelease));

            $seatsActuallyReleased = [];
            foreach ($seatsToRelease as $seat) {
                if (in_array($seat, $currentHeldSeats)) {
                    $seatsActuallyReleased[] = $seat;
                } else {
                    // Kursi tidak ditemukan di held_seats, mungkin sudah dilepas oleh cron atau proses lain
                    Log::warning("DEBUG_KENDARAAN_RELEASE: Kursi $seat TIDAK DITEMUKAN di held_seats saat proses rilis.");
                }
            }

            if (empty($seatsActuallyReleased)) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada kursi yang valid untuk dilepas dari held_seats.',
                    'data' => $kendaraan->fresh()->toArray(),
                ], 422);
            }

            // Pindahkan kursi yang benar-benar ada di held_seats kembali ke available
            $newHeldSeats = array_values(array_diff($currentHeldSeats, $seatsActuallyReleased));
            $newAvailableSeats = array_unique(array_merge($currentAvailableSeats, $seatsActuallyReleased));
            sort($newAvailableSeats);
            sort($newHeldSeats);

            $kendaraan->available_seats = $newAvailableSeats;
            $kendaraan->held_seats = $newHeldSeats;
            $kendaraan->save();

            DB::commit();
            Log::info("DEBUG_KENDARAAN_RELEASE: Kursi berhasil dilepas. New Available Seats: " . json_encode($newAvailableSeats) . ", New Held Seats: " . json_encode($newHeldSeats));

            $updatedKendaraan = $kendaraan->fresh();
            if ($updatedKendaraan->gambar) {
                $updatedKendaraan->gambar = asset('storage/' . $updatedKendaraan->gambar);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Kursi berhasil dikembalikan ke tersedia.',
                'data' => $updatedKendaraan,
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error("DEBUG_KENDARAAN_RELEASE: Validation Error: " . json_encode($e->errors()));
            return response()->json([
                'status' => 'error',
                'message' => 'Kesalahan validasi',
                'errors' => $e->errors(),
                'data' => null,
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error("DEBUG_KENDARAAN_RELEASE: ModelNotFoundException: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Kendaraan tidak ditemukan.',
                'data' => null,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("DEBUG_KENDARAAN_RELEASE: General Error: " . $e->getMessage() . " on line " . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengembalikan kursi: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    // --- Bagian Admin untuk Kendaraan (CRUD) ---
    public function indexAdmin()
    {
        $kendaraans = Kendaraan::with('destinasi')->get();
        $kendaraans->each(function ($kendaraan) {
            $kendaraan->available_seats = collect($kendaraan->available_seats ?? [])->map(fn($item) => (int) $item)->toArray();
            $kendaraan->held_seats = collect($kendaraan->held_seats ?? [])->map(fn($item) => (int) $item)->toArray();
        });
        return view('admin.kendaraan.index', compact('kendaraans'));
    }

    public function createAdmin()
    {
        $destinasis = Destinasi::all();
        return view('admin.kendaraan.create', compact('destinasis'));
    }

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
            'available_seats' => array_values(range(1, (int)$request->kapasitas)),
            'held_seats' => [], // Inisialisasi held_seats kosong
        ]);

        return redirect()->route('admin.kendaraan.index')->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function editAdmin(Kendaraan $kendaraan)
    {
        $destinasis = Destinasi::all();
        return view('admin.kendaraan.edit', compact('kendaraan', 'destinasis'));
    }

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

        $currentAvailableSeats = collect($kendaraan->available_seats ?? [])->map(fn($item) => (int) $item)->toArray();
        $currentHeldSeats = collect($kendaraan->held_seats ?? [])->map(fn($item) => (int) $item)->toArray();

        if ($request->kapasitas != $kendaraan->kapasitas) {
            $newCapacity = (int)$request->kapasitas;
            $newAvailableSeats = [];
            $newHeldSeatsAfterCapacityChange = [];

            // Pertahankan kursi yang masih valid dalam kapasitas baru
            foreach ($currentAvailableSeats as $seat) {
                if ($seat <= $newCapacity) {
                    $newAvailableSeats[] = $seat;
                }
            }
            foreach ($currentHeldSeats as $seat) {
                if ($seat <= $newCapacity) {
                    $newHeldSeatsAfterCapacityChange[] = $seat;
                }
            }

            // Tambahkan kursi baru jika kapasitas meningkat
            for ($i = $kendaraan->kapasitas + 1; $i <= $newCapacity; $i++) {
                // Hanya tambahkan ke available jika belum ada di held
                if (!in_array($i, $newHeldSeatsAfterCapacityChange)) {
                    $newAvailableSeats[] = $i;
                }
            }
            sort($newAvailableSeats);
            sort($newHeldSeatsAfterCapacityChange); // Sort held seats too
            $availableSeatsToSave = $newAvailableSeats;
            $heldSeatsToSave = $newHeldSeatsAfterCapacityChange;
        } else {
            $availableSeatsToSave = $currentAvailableSeats;
            $heldSeatsToSave = $currentHeldSeats;
        }

        $kendaraan->update([
            'destinasi_id' => $request->destinasi_id,
            'jenis' => $request->jenis,
            'tipe' => $request->tipe,
            'kapasitas' => $request->kapasitas,
            'harga' => $request->harga,
            'fasilitas' => $request->fasilitas,
            'gambar' => $imagePath,
            'available_seats' => $availableSeatsToSave,
            'held_seats' => $heldSeatsToSave, // Simpan held seats
        ]);

        return redirect()->route('admin.kendaraan.index')->with('success', 'Kendaraan berhasil diperbarui.');
    }

    public function destroyAdmin(Kendaraan $kendaraan)
    {
        if ($kendaraan->gambar && Storage::disk('public')->exists($kendaraan->gambar)) {
            Storage::disk('public')->delete($kendaraan->gambar);
        }
        $kendaraan->delete();
        return redirect()->route('admin.kendaraan.index')->with('success', 'Kendaraan berhasil dihapus.');
    }
}
