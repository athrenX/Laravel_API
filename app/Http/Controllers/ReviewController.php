<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Destinasi;
use App\Models\User;
use Illuminate\Support\Facades\URL; // <-- WAJIB: Tambahkan ini untuk URL helper

class ReviewController extends Controller
{
    /**
     * Menyimpan review baru dan memperbarui rating destinasi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'destinasi_id' => 'required|exists:destinasis,id',
            'order_id' => 'required|string',
            'user_name' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        // Opsional: Jika Anda ingin menyimpan user_profile_picture_url langsung di tabel 'reviews'
        // dan kolom 'user_profile_picture_url' sudah ada di tabel 'reviews'.
        $user = User::find($validated['user_id']);
        if ($user && $user->foto_profil) {
            // Menggunakan URL::to() untuk mendapatkan URL lengkap
            $validated['user_profile_picture_url'] = URL::to('storage/' . $user->foto_profil); // <-- PERBAIKAN DI SINI
        } else {
             $validated['user_profile_picture_url'] = null; // Pastikan null jika tidak ada foto
        }

        $review = Review::create($validated);

        $this->_updateDestinasiRating($validated['destinasi_id']);

        return response()->json(['success' => true, 'review' => $review], 201);
    }

    /**
     * Memperbarui review yang sudah ada dan memperbarui rating destinasi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        $destinasiId = $review->destinasi_id;

        // Opsional: Jika Anda menyimpan user_profile_picture_url di tabel 'reviews'
        $user = User::find($review->user_id);
        if ($user && $user->foto_profil) {
             $validated['user_profile_picture_url'] = URL::to('storage/' . $user->foto_profil); // <-- PERBAIKAN DI SINI
        } else {
             $validated['user_profile_picture_url'] = null;
        }

        $review->update($validated);

        $this->_updateDestinasiRating($destinasiId);

        return response()->json(['success' => true, 'review' => $review]);
    }

    /**
     * Menampilkan review berdasarkan ID pesanan.
     *
     * @param  string  $order_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showByOrder($order_id)
    {
        $review = Review::where('order_id', $order_id)
                        ->with(['user:id,foto_profil']) // Masih menggunakan relasi
                        ->first();

        if (!$review) {
            return response()->json(['message' => 'Review not found for this order ID.'], 404);
        }

        $reviewData = $review->toArray();
        if ($review->user && $review->user->foto_profil) {
            // Menggunakan URL::to() untuk membuat URL lengkap
            $reviewData['user_profile_picture_url'] = URL::to('storage/' . $review->user->foto_profil); // <-- PERBAIKAN DI SINI
        } else {
            $reviewData['user_profile_picture_url'] = null;
        }
        unset($reviewData['user']); // Hapus objek user lengkap

        return response()->json(['review' => $reviewData]);
    }

    /**
     * Mendapatkan semua review untuk suatu destinasi.
     * Ini digunakan oleh frontend DetailDestinasiScreen.
     *
     * @param  string|int  $destinasi_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReviewsByDestinasi($destinasi_id)
    {
        // Lakukan join dengan tabel users untuk mendapatkan foto profil
        $reviews = Review::where('reviews.destinasi_id', $destinasi_id)
                        ->join('users', 'reviews.user_id', '=', 'users.id')
                        ->select(
                            'reviews.*',
                            'users.foto_profil' // <-- Pilih kolom foto_profil mentah
                        )
                        ->get()
                        ->map(function($review) {
                            // Tambahkan URL lengkap foto profil ke setiap objek review
                            if ($review->foto_profil) {
                                $review->user_profile_picture_url = URL::to('storage/' . $review->foto_profil); // <-- PERBAIKAN DI SINI
                            } else {
                                $review->user_profile_picture_url = null;
                            }
                            unset($review->foto_profil); // Hapus foto_profil mentah jika tidak diperlukan di respons
                            return $review;
                        });

        return response()->json($reviews);
    }

    /**
     * Metode private untuk menghitung dan memperbarui rating rata-rata destinasi.
     *
     * @param  string|int  $destinasiId
     * @return void
     */
    private function _updateDestinasiRating($destinasiId)
    {
        $destinasi = Destinasi::find($destinasiId);

        if ($destinasi) {
            $reviews = Review::where('destinasi_id', $destinasiId)->get();

            if ($reviews->isNotEmpty()) {
                $averageRating = $reviews->avg('rating');
                $destinasi->rating = round($averageRating, 1);
            } else {
                $destinasi->rating = 0.0;
            }

            $destinasi->save();
        }
    }
}
