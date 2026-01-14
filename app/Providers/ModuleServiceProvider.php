<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $modulesPath = app_path('Modules');

        if (! is_dir($modulesPath)) {
            return;
        }

        foreach (scandir($modulesPath) as $module) {
            if ($module === '.' || $module === '..') {
                continue;
            }

            $routesPath = "{$modulesPath}/{$module}/Routes";

            if (! is_dir($routesPath)) {
                continue;
            }

            foreach (scandir($routesPath) as $file) {
                if (! preg_match('/^v\d+\.php$/', $file)) {
                    continue;
                }

                // extract version number (v1.php → v1)
                $version = pathinfo($file, PATHINFO_FILENAME);

                Route::middleware('api')
                    ->prefix("api/{$version}/" . Str::kebab($module))
                    ->group("{$routesPath}/{$file}");
            }
        }
    }
}
