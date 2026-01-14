<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(!$this->app->isProduction());
        
        Model::preventLazyLoading(!$this->app->isProduction());
        
        Model::preventSilentlyDiscardingAttributes(!$this->app->isProduction());
        
        Model::preventAccessingMissingAttributes(!$this->app->isProduction());
        
        $this->configureRateLimiting();

        $modulesPath = app_path('Modules');

        foreach (glob("$modulesPath/*/Database/Migrations") as $path) {
            $this->loadMigrationsFrom($path);
        }
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return [
                Limit::perSecond(10)->by($request->user()?->id ?: $request->ip()),
                Limit::perMinute(100)->by($request->user()?->id ?: $request->ip()),
            ];
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many authentication attempts. Please try again later.',
                    ], 429);
                });
        });

        RateLimiter::for('content', function (Request $request) {
            return Limit::perMinute(120)
                ->by($request->user()?->id ?: $request->ip());
        });
    }
}
