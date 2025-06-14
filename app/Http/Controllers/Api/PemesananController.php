<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str; 

class PemesananController extends Controller
{
    /**
     * API: Menyimpan pemesanan baru yang dibuat oleh user.
     * Kursi sudah di-hold di KendaraanController@holdSeats.
     * Pada tahap ini, status pemesanan adalah 'menunggu pembayaran'.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'destinasi_id' => 'required|exists:destinasis,id',
                'kendaraan_id' => 'required|exists:kendaraans,id',
                'selected_seats' => 'required|array',
                'selected_seats.*' => 'integer|min:1',
                'jumlah_peserta' => 'required|integer|min:1',
                'total_harga' => 'required|numeric|min:0',
            ]);

            if (count($validated['selected_seats']) != $validated['jumlah_peserta']) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jumlah kursi yang dipilih harus sama dengan jumlah peserta.',
                    'data' => null,
                ], 422);
            }

            $kendaraan = Kendaraan::where('id', $validated['kendaraan_id'])->lockForUpdate()->firstOrFail();

            $currentHeldSeats = collect($kendaraan->held_seats ?? [])->map(fn($item) => (int) $item)->toArray();
            $seatsToBook = collect($validated['selected_seats'])->map(fn($item) => (int) $item)->toArray();

            Log::info("DEBUG_PEMESANAN_STORE: PemesananController@store for Kendaraan ID: {$validated['kendaraan_id']}");
            Log::info("DEBUG_PEMESANAN_STORE: Current Held Seats from DB (LOCKED): " . json_encode($currentHeldSeats));
            Log::info("DEBUG_PEMESANAN_STORE: Selected Seats from Request: " . json_encode($seatsToBook));

            // Verifikasi bahwa semua kursi yang dipilih sudah ada di held_seats
            foreach ($seatsToBook as $seat) {
                if (!in_array($seat, $currentHeldSeats)) {
                    DB::rollBack();
                    Log::warning("DEBUG_PEMESANAN_STORE: Kursi $seat TIDAK DITEMUKAN di held_seats. Ini menandakan holdSeats tidak berhasil atau sudah dilepas.");
                    return response()->json([
                        'status' => 'error',
                        'message' => "Kursi $seat tidak lagi ditahan atau sudah kadaluarsa. Silakan refresh dan pilih kursi lain.",
                        'data' => $kendaraan->fresh()->toArray(),
                    ], 409);
                }
            }

            // Waktu kadaluarsa untuk pembayaran (misal 30 menit dari sekarang)
            $expirationTime = Carbon::now()->addMinutes(30);

            $pemesanan = Pemesanan::create([
                'user_id' => Auth::id(),
                'destinasi_id' => $validated['destinasi_id'],
                'kendaraan_id' => $validated['kendaraan_id'],
                'selected_seats' => $seatsToBook,
                'jumlah_peserta' => $validated['jumlah_peserta'],
                'tanggal_pemesanan' => now(),
                'total_harga' => $validated['total_harga'],
                'status' => 'menunggu pembayaran',
                'expired_at' => $expirationTime,
            ]);

            DB::commit();
            Log::info("DEBUG_PEMESANAN_STORE: Pemesanan berhasil dibuat. Pemesanan ID: {$pemesanan->id}. Kursi ditahan di held_seats: " . json_encode($seatsToBook));

            return response()->json([
                'status' => 'success',
                'message' => 'Pemesanan berhasil dibuat. Silakan selesaikan pembayaran dalam ' . $expirationTime->diffForHumans(Carbon::now(), true) . '.',
                'data' => $pemesanan->load('destinasi', 'kendaraan', 'user'),
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error("DEBUG_PEMESANAN_STORE: Validation Error: " . json_encode($e->errors()));
            return response()->json([
                'status' => 'error',
                'message' => 'Kesalahan validasi',
                'errors' => $e->errors(),
                'data' => null,
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("DEBUG_PEMESANAN_STORE: General Error: " . $e->getMessage() . " on line " . $e->getLine());
            // Jika ada error di sini setelah kursi berhasil di-hold, Anda mungkin ingin melepas kursinya kembali.
            // Ini bisa dilakukan dengan memanggil releaseHeldSeats dari sini jika $seatsToBook sudah diketahui.
            // Untuk kesederhanaan dan keandalan, disarankan menggunakan cron job untuk rilis otomatis.
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat pemesanan: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * API: Mengkonfirmasi pembayaran pemesanan.
     * Memindahkan kursi dari held_seats ke available_seats (sehingga tidak lagi tersedia).
     */
    public function confirmPayment(Request $request, $pemesananId)
    {
        DB::beginTransaction();

        try {
            $pemesanan = Pemesanan::where('id', $pemesananId)->lockForUpdate()->firstOrFail();

            if ($pemesanan->status === 'dibayar' || $pemesanan->status === 'selesai' || $pemesanan->status === 'diproses') {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pemesanan ini sudah dibayar atau sedang diproses.',
                ], 409);
            }

            if ($pemesanan->status === 'dibatalkan') {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pemesanan ini sudah dibatalkan.',
                ], 409);
            }

            if ($pemesanan->expired_at && Carbon::now()->greaterThan($pemesanan->expired_at)) {
                DB::rollBack();
                // Opsional: langsung batalkan pemesanan dan kembalikan kursi jika kadaluarsa
                $this->cancelExpiredPemesanan($pemesanan); // Panggil helper untuk membatalkan dan merilis kursi
                return response()->json([
                    'status' => 'error',
                    'message' => 'Waktu pembayaran telah habis. Pemesanan dibatalkan.',
                ], 410); // 410 Gone status code
            }

            $kendaraan = Kendaraan::where('id', $pemesanan->kendaraan_id)->lockForUpdate()->firstOrFail();

            $currentAvailableSeats = collect($kendaraan->available_seats ?? [])->map(fn($item) => (int) $item)->toArray();
            $currentHeldSeats = collect($kendaraan->held_seats ?? [])->map(fn($item) => (int) $item)->toArray();
            $bookedSeats = collect($pemesanan->selected_seats)->map(fn($item) => (int) $item)->toArray();

            Log::info("DEBUG_PEMESANAN_CONFIRM: Confirming payment for Pemesanan ID: $pemesananId");
            Log::info("DEBUG_PEMESANAN_CONFIRM: Current Held Seats (LOCKED): " . json_encode($currentHeldSeats));
            Log::info("DEBUG_PEMESANAN_CONFIRM: Seats to Confirm: " . json_encode($bookedSeats));

            // Pastikan kursi yang akan dikonfirmasi masih ada di held_seats
            foreach ($bookedSeats as $seat) {
                if (!in_array($seat, $currentHeldSeats)) {
                    DB::rollBack();
                    Log::warning("DEBUG_PEMESANAN_CONFIRM: Kursi $seat TIDAK DITEMUKAN di held_seats saat konfirmasi pembayaran. Mungkin sudah dilepas.");
                    return response()->json([
                        'status' => 'error',
                        'message' => "Kursi $seat tidak lagi ditahan atau sudah dibatalkan. Pembayaran gagal.",
                        'data' => $kendaraan->fresh()->toArray(),
                    ], 409);
                }
            }

            // Hapus kursi dari held_seats (karena sudah 'booked' dan tidak perlu di hold lagi)
            // Kursi yang sudah dibayar tidak akan ada di available_seats atau held_seats.
            $newHeldSeats = array_values(array_diff($currentHeldSeats, $bookedSeats));
            sort($newHeldSeats);

            $kendaraan->held_seats = $newHeldSeats;
            $kendaraan->save();

            $pemesanan->status = 'dibayar'; // Atau 'diproses'
            $pemesanan->save();

            DB::commit();
            Log::info("DEBUG_PEMESANAN_CONFIRM: Pembayaran dikonfirmasi. Pemesanan ID: {$pemesanan->id}. New Held Seats for Kendaraan {$kendaraan->id}: " . json_encode($newHeldSeats));

            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran berhasil dikonfirmasi. Pemesanan Anda sedang diproses.',
                'data' => $pemesanan->load('destinasi', 'kendaraan', 'user'),
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error("DEBUG_PEMESANAN_CONFIRM: ModelNotFoundException: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Pemesanan tidak ditemukan.',
                'data' => null,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("DEBUG_PEMESANAN_CONFIRM: General Error: " . $e->getMessage() . " on line " . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengkonfirmasi pembayaran: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * API: Membatalkan pemesanan.
     * Mengembalikan kursi dari held_seats atau selected_seats ke available_seats.
     */
    public function cancelPemesanan($pemesananId)
    {
        DB::beginTransaction();

        try {
            $pemesanan = Pemesanan::where('id', $pemesananId)->lockForUpdate()->firstOrFail();

            if ($pemesanan->status === 'dibayar' || $pemesanan->status === 'diproses' || $pemesanan->status === 'selesai') {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pemesanan tidak dapat dibatalkan karena sudah diproses atau selesai.',
                ], 403);
            }

            if ($pemesanan->status === 'dibatalkan') {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pemesanan ini sudah dibatalkan sebelumnya.',
                ], 409);
            }

            $kendaraan = Kendaraan::where('id', $pemesanan->kendaraan_id)->lockForUpdate()->firstOrFail();

            $currentAvailableSeats = collect($kendaraan->available_seats ?? [])->map(fn($item) => (int) $item)->toArray();
            $currentHeldSeats = collect($kendaraan->held_seats ?? [])->map(fn($item) => (int) $item)->toArray();
            $seatsToReturn = collect($pemesanan->selected_seats)->map(fn($item) => (int) $item)->toArray();

            Log::info("DEBUG_PEMESANAN_CANCEL: Cancelling Pemesanan ID: $pemesananId");
            Log::info("DEBUG_PEMESANAN_CANCEL: Current Available Seats: " . json_encode($currentAvailableSeats));
            Log::info("DEBUG_PEMESANAN_CANCEL: Current Held Seats: " . json_encode($currentHeldSeats));
            Log::info("DEBUG_PEMESANAN_CANCEL: Seats to Return: " . json_encode($seatsToReturn));

            // Periksa apakah kursi yang akan dikembalikan ada di held_seats
            $seatsFoundInHeld = array_intersect($seatsToReturn, $currentHeldSeats);

            $newHeldSeats = array_values(array_diff($currentHeldSeats, $seatsFoundInHeld)); // Hapus dari held_seats
            $newAvailableSeats = array_unique(array_merge($currentAvailableSeats, $seatsToReturn)); // Tambahkan ke available_seats

            sort($newAvailableSeats);
            sort($newHeldSeats);

            $kendaraan->available_seats = $newAvailableSeats;
            $kendaraan->held_seats = $newHeldSeats;
            $kendaraan->save();

            $pemesanan->status = 'dibatalkan';
            $pemesanan->save();

            DB::commit();
            Log::info("DEBUG_PEMESANAN_CANCEL: Pemesanan berhasil dibatalkan. Pemesanan ID: {$pemesanan->id}. Kursi dikembalikan.");

            return response()->json([
                'status' => 'success',
                'message' => 'Pemesanan berhasil dibatalkan dan kursi dikembalikan.',
                'data' => $pemesanan->load('destinasi', 'kendaraan', 'user'),
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error("DEBUG_PEMESANAN_CANCEL: ModelNotFoundException: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Pemesanan tidak ditemukan.',
                'data' => null,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("DEBUG_PEMESANAN_CANCEL: General Error: " . $e->getMessage() . " on line " . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membatalkan pemesanan: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    protected function cancelExpiredPemesanan(Pemesanan $pemesanan)
    {
        DB::beginTransaction();
        try {
            $pemesanan->refresh()->lockForUpdate();

            if ($pemesanan->status === 'menunggu pembayaran' && $pemesanan->expired_at && Carbon::now()->greaterThan($pemesanan->expired_at)) {
                $kendaraan = Kendaraan::where('id', $pemesanan->kendaraan_id)->lockForUpdate()->firstOrFail();

                $currentAvailableSeats = collect($kendaraan->available_seats ?? [])->map(fn($item) => (int) $item)->toArray();
                $currentHeldSeats = collect($kendaraan->held_seats ?? [])->map(fn($item) => (int) $item)->toArray();
                $seatsToReturn = collect($pemesanan->selected_seats)->map(fn($item) => (int) $item)->toArray();

                $newHeldSeats = array_values(array_diff($currentHeldSeats, $seatsToReturn));
                $newAvailableSeats = array_unique(array_merge($currentAvailableSeats, $seatsToReturn));

                sort($newAvailableSeats);
                sort($newHeldSeats);

                $kendaraan->available_seats = $newAvailableSeats;
                $kendaraan->held_seats = $newHeldSeats;
                $kendaraan->save();

                $pemesanan->status = 'dibatalkan';
                $pemesanan->save();
                DB::commit();
                Log::info("Pemesanan ID: {$pemesanan->id} dibatalkan otomatis karena kadaluarsa.");
                return true;
            }
            DB::rollBack();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error cancelling expired pemesanan {$pemesanan->id}: " . $e->getMessage());
            return false;
        }
    }


    public function index(Request $request)
    {
        $query = Pemesanan::with(['user', 'destinasi', 'kendaraan']);

        $isAdmin = Auth::check() && Auth::user()->is_admin;

        if (!$isAdmin) {
            $query->where('user_id', Auth::id());
        }

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('destinasi', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            })->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $pemesanans = $query->orderBy('created_at', 'desc')->get();

        $pemesanans->each(function ($pemesanan) {
        
            if ($pemesanan->kendaraan && $pemesanan->kendaraan->gambar) {
                if (!Str::startsWith($pemesanan->kendaraan->gambar, ['http://', 'https://'])) {
                    $pemesanan->kendaraan->gambar = asset('storage/' . $pemesanan->kendaraan->gambar);
                }
            }
        
            if ($pemesanan->kendaraan) {
                $pemesanan->kendaraan->available_seats = collect($pemesanan->kendaraan->available_seats ?? [])->map(function ($item) {
                    return (int) $item;
                })->toArray();
                $pemesanan->kendaraan->held_seats = collect($pemesanan->kendaraan->held_seats ?? [])->map(function ($item) {
                    return (int) $item;
                })->toArray();
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar pemesanan berhasil diambil.',
            'data' => $pemesanans,
        ]);
    }


    public function show($id)
    {
        try {
            $pemesanan = Pemesanan::with(['user', 'destinasi', 'kendaraan'])->findOrFail($id);

            if (Auth::id() != $pemesanan->user_id && !Auth::user()->is_admin) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk melihat pemesanan ini.',
                    'data' => null,
                ], 403);
            }

            if ($pemesanan->destinasi->gambar) {
                $pemesanan->destinasi->gambar = asset('storage/' . $pemesanan->destinasi->gambar);
            }
            if ($pemesanan->kendaraan->gambar) {
                $pemesanan->kendaraan->gambar = asset('storage/' . $pemesanan->kendaraan->gambar);
            }
            if ($pemesanan->kendaraan) {
                $pemesanan->kendaraan->available_seats = collect($pemesanan->kendaraan->available_seats ?? [])->map(function($item) {
                    return (int) $item;
                })->toArray();
                $pemesanan->kendaraan->held_seats = collect($pemesanan->kendaraan->held_seats ?? [])->map(function($item) {
                    return (int) $item;
                })->toArray();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Detail pemesanan berhasil diambil.',
                'data' => $pemesanan,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pemesanan tidak ditemukan.',
                'data' => null,
            ], 404);
        } catch (\Exception $e) {
            Log::error("Error in show Pemesanan: " . $e->getMessage() . " on line " . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil detail pemesanan: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $pemesanan = Pemesanan::findOrFail($id);

            if (Auth::id() != $pemesanan->user_id && !Auth::user()->is_admin) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk memperbarui pemesanan ini.',
                ], 403);
            }

            $validated = $request->validate([
                'status' => 'required|string|in:pending,menunggu pembayaran,dibayar,diproses,selesai,dibatalkan',
            ]);

            $oldStatus = $pemesanan->status;
            $newStatus = $validated['status'];

            $seatsToProcess = collect($pemesanan->selected_seats)->map(fn($item) => (int) $item)->toArray();
            $kendaraan = Kendaraan::where('id', $pemesanan->kendaraan_id)->lockForUpdate()->firstOrFail();
            $currentAvailableSeats = collect($kendaraan->available_seats ?? [])->map(fn($item) => (int) $item)->toArray();
            $currentHeldSeats = collect($kendaraan->held_seats ?? [])->map(fn($item) => (int) $item)->toArray();

            if ($oldStatus === 'menunggu pembayaran' && $newStatus === 'dibayar') {
                // Konfirmasi pembayaran: pindahkan dari held_seats
                $newHeldSeats = array_values(array_diff($currentHeldSeats, $seatsToProcess));
                sort($newHeldSeats);
                $kendaraan->held_seats = $newHeldSeats;
                $kendaraan->save();
                Log::info("DEBUG_PEMESANAN_UPDATE: Kursi {$pemesanan->id} dipindahkan dari held_seats karena pembayaran dikonfirmasi.");
            } elseif (($oldStatus === 'menunggu pembayaran' || $oldStatus === 'pending') && $newStatus === 'dibatalkan') {
                // Pembatalan sebelum atau saat menunggu pembayaran: kembalikan dari held_seats ke available_seats
                $newHeldSeats = array_values(array_diff($currentHeldSeats, $seatsToProcess));
                $newAvailableSeats = array_unique(array_merge($currentAvailableSeats, $seatsToProcess));
                sort($newAvailableSeats);
                sort($newHeldSeats);
                $kendaraan->available_seats = $newAvailableSeats;
                $kendaraan->held_seats = $newHeldSeats;
                $kendaraan->save();
                Log::info("DEBUG_PEMESANAN_UPDATE: Kursi {$pemesanan->id} dikembalikan ke available_seats karena pemesanan dibatalkan.");
            } elseif ($oldStatus === 'dibayar' && $newStatus === 'dibatalkan') {
                // Pembatalan setelah dibayar (admin action): kembalikan ke available_seats
                // Ini mungkin membutuhkan kebijakan refund
                $newAvailableSeats = array_unique(array_merge($currentAvailableSeats, $seatsToProcess));
                sort($newAvailableSeats);
                $kendaraan->available_seats = $newAvailableSeats;
                $kendaraan->save();
                Log::info("DEBUG_PEMESANAN_UPDATE: Kursi {$pemesanan->id} dikembalikan ke available_seats karena dibatalkan setelah pembayaran.");
            }

            $pemesanan->status = $newStatus;
            $pemesanan->save();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Pemesanan berhasil diperbarui.',
                'data' => $pemesanan->load('destinasi', 'kendaraan', 'user'),
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error("DEBUG_PEMESANAN_UPDATE: Validation Error: " . json_encode($e->errors()));
            return response()->json([
                'status' => 'error',
                'message' => 'Kesalahan validasi',
                'errors' => $e->errors(),
                'data' => null,
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("DEBUG_PEMESANAN_UPDATE: General Error: " . $e->getMessage() . " on line " . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui pemesanan: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $pemesanan = Pemesanan::findOrFail($id);

            if (Auth::id() != $pemesanan->user_id && !Auth::user()->is_admin) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk menghapus pemesanan ini.',
                ], 403);
            }

            if ($pemesanan->status != 'dibatalkan' && $pemesanan->status != 'selesai') {
                $kendaraan = Kendaraan::where('id', $pemesanan->kendaraan_id)->lockForUpdate()->firstOrFail();
                $currentAvailableSeats = collect($kendaraan->available_seats ?? [])->map(fn($item) => (int) $item)->toArray();
                $currentHeldSeats = collect($kendaraan->held_seats ?? [])->map(fn($item) => (int) $item)->toArray();
                $returnedSeats = collect($pemesanan->selected_seats)->map(fn($item) => (int) $item)->toArray();

                $newHeldSeats = array_values(array_diff($currentHeldSeats, $returnedSeats)); // Hapus dari held_seats
                $newAvailableSeats = array_unique(array_merge($currentAvailableSeats, $returnedSeats)); // Tambahkan ke available_seats

                sort($newAvailableSeats);
                sort($newHeldSeats);

                $kendaraan->available_seats = $newAvailableSeats;
                $kendaraan->held_seats = $newHeldSeats;
                $kendaraan->save();
                Log::info("DEBUG_PEMESANAN_DESTROY: Kursi dikembalikan karena pemesanan dihapus. Kendaraan ID: {$kendaraan->id}, Kursi: " . json_encode($returnedSeats));
            }

            $pemesanan->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Pemesanan berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("DEBUG_PEMESANAN_DESTROY: Error: " . $e->getMessage() . " on line " . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus pemesanan: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function editAdmin(Pemesanan $pemesanan)
    {
        return view('admin.pemesanan.edit', compact('pemesanan'));
    }
}
