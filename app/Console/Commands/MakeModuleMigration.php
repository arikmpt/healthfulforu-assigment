<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleMigration extends Command
{
    protected $signature = 'make:module-migration {module} {name}';
    protected $description = 'Create a migration inside a module';

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $name = $this->argument('name');

        $path = app_path("Modules/{$module}/Database/Migrations");
        File::ensureDirectoryExists($path);

        $timestamp = date('Y_m_d_His');
        $file = "{$path}/{$timestamp}_{$name}.php";

        File::put($file, <<<PHP
        <?php

        use Illuminate\\Database\\Migrations\\Migration;
        use Illuminate\\Database\\Schema\\Blueprint;
        use Illuminate\\Support\\Facades\\Schema;

        return new class extends Migration {
            public function up(): void
            {
                Schema::create('{$this->guessTable($name)}', function (Blueprint \$table) {
                    \$table->id();
                    \$table->timestamps();
                });
            }

            public function down(): void
            {
                Schema::dropIfExists('{$this->guessTable($name)}');
            }
        };

        PHP);

        $this->info("Migration created: {$file}");
        return self::SUCCESS;
    }

    protected function guessTable(string $name): string
    {
        return Str::snake(str_replace(['create_', '_table'], '', $name));
    }
}
