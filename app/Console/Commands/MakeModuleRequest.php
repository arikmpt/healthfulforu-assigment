<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleRequest extends Command
{
    protected $signature = 'make:module-request {module} {name}';
    protected $description = 'Create a FormRequest inside a module';

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $name = Str::studly($this->argument('name'));

        $path = app_path("Modules/{$module}/Http/Requests");
        File::ensureDirectoryExists($path);

        $file = "{$path}/{$name}.php";

        if (File::exists($file)) {
            $this->error('Request already exists');
            return self::FAILURE;
        }

        File::put($file, <<<PHP
        <?php

        namespace Modules\\{$module}\\Http\\Requests;

        use Illuminate\\Foundation\\Http\\FormRequest;

        class {$name} extends FormRequest
        {
            public function rules(): array
            {
                return [];
            }
        }

        PHP);

        $this->info("Request created: {$file}");
        return self::SUCCESS;
    }
}
