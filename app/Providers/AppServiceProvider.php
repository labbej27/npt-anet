<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

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
         Carbon::setLocale(config('app.locale', 'fr'));
    CarbonImmutable::setLocale(config('app.locale', 'fr'));
    // (optionnel)
    setlocale(LC_TIME, 'fr_FR.UTF-8');
    }
}
