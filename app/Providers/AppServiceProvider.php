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

        \Illuminate\Database\Eloquent\Model::preventLazyLoading(! $this->app->isProduction());

        \Illuminate\Support\Facades\View::composer('layouts.admin', function ($view) {
            $view->with('pendingOrdersCount', \App\Models\Order::where('status', 'pending')->count());
            $view->with('unapprovedReviewsCount', \App\Models\Review::where('is_approved', false)->count());
        });
    }
}
