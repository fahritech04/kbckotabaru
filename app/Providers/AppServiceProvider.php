<?php

namespace App\Providers;

use App\Services\SessionAuthService;
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
        View::composer('*', function ($view): void {
            $view->with('adminAuthUser', session(SessionAuthService::SESSION_KEY_ADMIN));
            $view->with('clubAuthUser', session(SessionAuthService::SESSION_KEY_CLUB));
        });
    }
}
