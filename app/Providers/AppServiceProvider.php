<?php

namespace App\Providers;

use App\Helpers\BreadcrumbHelper;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
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
        $this->configureRateLimiting();
        $this->configureViewComposers();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Rate limiting for search endpoints
        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        // Rate limiting for item submissions
        RateLimiter::for('submissions', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(5)->by($request->user()->id)
                : Limit::perMinute(2)->by($request->ip());
        });

        // Rate limiting for admin actions
        RateLimiter::for('admin', function (Request $request) {
            return Limit::perMinute(100)->by($request->user()->id ?? $request->ip());
        });

        // General API rate limiting
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    /**
     * Configure view composers for the application.
     */
    protected function configureViewComposers(): void
    {
        // Share breadcrumbs with all views
        View::composer('*', function ($view) {
            if (request()->route()) {
                $breadcrumbs = BreadcrumbHelper::generate();
                $view->with('breadcrumbs', $breadcrumbs);
            }
        });
    }
}
