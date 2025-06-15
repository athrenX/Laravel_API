<?php

// app/Http/Controllers/ReviewController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'destinasi_id' => 'required',
            'order_id' => 'required',
            'user_name' => 'required',
            'comment' => 'required',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        // Cek apakah user sudah pernah review order ini
        $existing = Review::where('order_id', $validated['order_id'])
            ->where('user_id', $validated['user_id'])
            ->first();
        if ($existing) {
            return response()->json(['message' => 'Review already exists.'], 409);
        }

        $review = Review::create($validated);
        return response()->json($review, 201);
    } 

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        $review->update($validated);

        return response()->json(['success' => true, 'review' => $review]);
    }

    public function showByOrder($order_id)
    {
        $review = Review::where('order_id', $order_id)->first();
        return response()->json(['review' => $review]);
    }
}
