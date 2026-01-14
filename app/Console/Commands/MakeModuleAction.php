<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleAction extends Command
{
    protected $signature = 'make:module-action {module} {name}';
    protected $description = 'Create an Action inside a module';

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $name = Str::studly($this->argument('name'));

        $path = app_path("Modules/{$module}/Actions");
        File::ensureDirectoryExists($path);

        $file = "{$path}/{$name}.php";

        if (File::exists($file)) {
            $this->error('Action already exists');
            return self::FAILURE;
        }

        File::put($file, <<<PHP
        <?php

        namespace Modules\\{$module}\\Actions;

        class {$name}
        {
            public function execute()
            {
                //
            }
        }

        PHP);

        $this->info("Action created: {$file}");
        return self::SUCCESS;
    }
}
