<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleResource extends Command
{
    protected $signature = 'make:module-resource {module} {name}';
    protected $description = 'Create a JSON Resource inside a module';

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $name = Str::studly($this->argument('name'));

        $path = app_path("Modules/{$module}/Http/Resources");
        File::ensureDirectoryExists($path);

        $file = "{$path}/{$name}.php";

        File::put($file, <<<PHP
        <?php

        namespace Modules\\{$module}\\Http\\Resources;

        use Illuminate\\Http\\Resources\\Json\\JsonResource;

        class {$name} extends JsonResource
        {
            public function toArray(\$request): array
            {
                return parent::toArray(\$request);
            }
        }

        PHP);

        $this->info("Resource created: {$file}");
        return self::SUCCESS;
    }
}
