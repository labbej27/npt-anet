<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        // Prend la locale de config/app.php (pilotée par .env)
        $locale = config('app.locale', 'fr');

        App::setLocale($locale);
        Carbon::setLocale($locale);
        CarbonImmutable::setLocale($locale);

        // (Optionnel) strftime / setlocale pour certains formats
        @setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');

        return $next($request);
    }
}
