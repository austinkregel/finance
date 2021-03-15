<?php

namespace App\Providers;

use App\Models\Alert;
use App\Services\ActionService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/home';

    public function boot(): void
    {
        Route::bind('action', fn ($value) => app(ActionService::class)->build($value));
        Route::bind('alert', fn ($value) => Alert::findOrFail($value));

        $this->routes(function (): void {
            Route::middleware('web')->group(base_path('routes/web.php'));
        });
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip()));
    }
}
