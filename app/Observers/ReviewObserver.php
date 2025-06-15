<?php

namespace App\Observers;

use App\Models\Review;

class ReviewObserver
{
    public function deleted(Review $review)
    {
        $destinasi = $review->destinasi;
        if ($destinasi) {
            $totalReview = $destinasi->reviews()->count();
            if ($totalReview > 0) {
                $avgRating = $destinasi->reviews()->avg('rating');
                $destinasi->rating = round($avgRating, 1);
            } else {
                $destinasi->rating = null;
            }
            $destinasi->save();
        }
    }
}
