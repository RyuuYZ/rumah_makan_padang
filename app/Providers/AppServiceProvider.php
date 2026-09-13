<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (request()->hasHeader('x-forwarded-host') || request()->hasHeader('x-forwarded-proto') || str_contains(request()->header('host', ''), 'loca.lt')) {
            \URL::forceScheme('https');
        }
    }
}
