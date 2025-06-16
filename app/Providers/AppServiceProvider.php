<?php

namespace App\Providers;
use App\Models\Review;
use App\Observers\ReviewObserver;


use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Review::observe(ReviewObserver::class);
    }
}
