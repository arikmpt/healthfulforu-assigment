<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleModel extends Command
{
    protected $signature = 'make:module-model
        {module : Module name (e.g. Content)}
        {name : Model name (e.g. Content)}
        {--migration : Generate migration file}';

    protected $description = 'Create a model inside a module';

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $name = Str::studly($this->argument('name'));

        $path = app_path("Modules/{$module}/Models");
        File::ensureDirectoryExists($path);

        $file = "{$path}/{$name}.php";

        if (File::exists($file)) {
            $this->error('Model already exists!');
            return self::FAILURE;
        }

        File::put($file, $this->stub($module, $name));

        if ($this->option('migration')) {
            $this->createMigration($module, $name);
        }

        $this->info("Model created: {$file}");

        return self::SUCCESS;
    }

    protected function stub(string $module, string $name): string
    {
        $table = Str::snake(Str::pluralStudly($name));

        return <<<PHP
        <?php

        namespace Modules\\{$module}\\Models;

        use Illuminate\\Database\\Eloquent\\Model;
        use Illuminate\\Database\\Eloquent\\SoftDeletes;

        class {$name} extends Model
        {
            use SoftDeletes;

            protected \$table = '{$table}';

            protected \$fillable = [
                //
            ];
        }

        PHP;
    }

    protected function createMigration(string $module, string $name): void
    {
        $table = Str::snake(Str::pluralStudly($name));
        $migrationName = "create_{$table}_table";
        $timestamp = date('Y_m_d_His');

        $path = app_path("Modules/{$module}/Database/Migrations");
        File::ensureDirectoryExists($path);

        $file = "{$path}/{$timestamp}_{$migrationName}.php";

        File::put($file, <<<PHP
        <?php

        use Illuminate\\Database\\Migrations\\Migration;
        use Illuminate\\Database\\Schema\\Blueprint;
        use Illuminate\\Support\\Facades\\Schema;

        return new class extends Migration {
            public function up(): void
            {
                Schema::create('{$table}', function (Blueprint \$table) {
                    \$table->id();
                    \$table->softDeletes();
                    \$table->timestamps();
                });
            }

            public function down(): void
            {
                Schema::dropIfExists('{$table}');
            }
        };

        PHP);

        $this->info("Migration created: {$file}");
    }
}
