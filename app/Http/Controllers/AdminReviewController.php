<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index()
    {
        // Ambil review beserta relasi user & destinasi
        $reviews = Review::with(['user', 'destinasi'])
            ->latest()
            ->paginate(12); // Pagination

        return view('admin.review', compact('reviews'));
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();
        // (Ingat, nama route disesuaikan dengan penamaan route kamu)
        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review berhasil dihapus dan rating destinasi terupdate.');
    }
}
