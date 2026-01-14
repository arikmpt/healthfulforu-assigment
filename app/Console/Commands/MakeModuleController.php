<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleController extends Command
{
    protected $signature = 'make:module-controller
        {module : Module name (e.g. Content)}
        {name : Controller name (e.g. ContentController)}';

    protected $description = 'Create a controller inside a module';

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $name = Str::studly($this->argument('name'));

        $path = app_path("Modules/{$module}/Http/Controllers");

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $file = "{$path}/{$name}.php";

        if (File::exists($file)) {
            $this->error("Controller already exists!");
            return self::FAILURE;
        }

        $stub = $this->getStub($module, $name);

        File::put($file, $stub);

        $this->info("Controller created: Modules/{$module}/Http/Controllers/{$name}.php");

        return self::SUCCESS;
    }

    protected function getStub(string $module, string $name): string
    {
        return $this->stub($module, $name);
    }

    protected function stub(string $module, string $name): string
    {
        return <<<PHP
        <?php

        namespace Modules\\{$module}\\Http\\Controllers;

        use App\\Http\\Controllers\\Controller;
        use Illuminate\\Http\\JsonResponse;

        class {$name} extends Controller
        {
            //
        }

        PHP;
    }

}