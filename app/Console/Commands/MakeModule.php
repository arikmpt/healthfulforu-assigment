<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModule extends Command
{
    protected $signature = 'make:module {name : Module name (e.g. Content)}';

    protected $description = 'Create a new application module with basic structure and routes';

    public function handle(): int
    {
        $module = Str::studly($this->argument('name'));
        $basePath = app_path("Modules/{$module}");

        if (File::exists($basePath)) {
            $this->error("Module {$module} already exists.");
            return self::FAILURE;
        }

        $this->createDirectories($basePath);
        $this->createRoutesFile($module, $basePath);
        $this->createController($module, $basePath);

        $this->info("Module {$module} created successfully.");

        return self::SUCCESS;
    }

    protected function createDirectories(string $basePath): void
    {
        $directories = [
            'Actions',
            'Models',
            'Services',
            'Http/Controllers',
            'Http/Requests',
            'Http/Resources',
            'Database/Migrations',
            'Database/Seeders',
            'Routes'
        ];

        foreach ($directories as $dir) {
            File::makeDirectory("{$basePath}/{$dir}", 0755, true);
        }
    }

    protected function createRoutesFile(string $module, string $basePath): void
    {
        $kebab = Str::kebab($module);
        $controller = "{$module}Controller";

        File::put("{$basePath}/Routes/v1.php", <<<PHP
        <?php

        use Illuminate\\Support\\Facades\\Route;
        use Modules\\{$module}\\Http\\Controllers\\{$controller};

        Route::apiResource('{$kebab}', {$controller}::class);

        PHP);
    }

    protected function createController(string $module, string $basePath): void
    {
        $controller = "{$module}Controller";

        File::put("{$basePath}/Http/Controllers/{$controller}.php", <<<PHP
        <?php

        namespace Modules\\{$module}\\Http\\Controllers;

        use App\\Http\\Controllers\\Controller;
        use Illuminate\\Http\\JsonResponse;

        class {$controller} extends Controller
        {
            public function index(): JsonResponse
            {
                return \$this->success([]);
            }

            public function store(): JsonResponse
            {
                return \$this->created();
            }

            public function show(): JsonResponse
            {
                return \$this->success([]);
            }

            public function update(): JsonResponse
            {
                return \$this->success();
            }

            public function destroy(): JsonResponse
            {
                return \$this->noContent();
            }
        }

        PHP);
    }
}
