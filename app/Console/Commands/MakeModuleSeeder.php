<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleSeeder extends Command
{
    protected $signature = 'make:module-seeder {module} {name}';
    protected $description = 'Create a seeder inside a module';

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $name = Str::studly($this->argument('name'));

        $path = app_path("Modules/{$module}/Database/Seeders");
        File::ensureDirectoryExists($path);

        $file = "{$path}/{$name}.php";

        File::put($file, <<<PHP
        <?php

        namespace Modules\\{$module}\\Database\\Seeders;

        use Illuminate\\Database\\Seeder;

        class {$name} extends Seeder
        {
            public function run(): void
            {
                //
            }
        }

        PHP);

        $this->info("Seeder created: {$file}");
        return self::SUCCESS;
    }
}
