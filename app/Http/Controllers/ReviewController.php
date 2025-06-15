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
            'user_id' => 'required|exists:users,id',
            'destinasi_id' => 'required|exists:destinasis,id',
            'order_id' => 'required',
            'user_name' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        $review = Review::create($validated);

        return response()->json(['success' => true, 'review' => $review]);
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

