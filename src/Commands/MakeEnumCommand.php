<?php

namespace DevCortes\LaravelCleanArchitecture\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeEnumCommand extends Command
{
    protected $signature = 'devcortes:enum {module : The name of the module} {name : The name of the enum}';

    protected $description = 'Create a new enum within a specified module';

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $enumName = Str::studly($this->argument('name'));
        $moduleName = Str::studly($this->argument('module'));

        $this->info("Creating enum: {$enumName} in module: {$moduleName}");

        $path = app_path("Src/Domain/{$moduleName}/Enums/{$enumName}.php");

        if ($this->files->exists($path)) {
            $this->error("Enum already exists at {$path}");
            return self::FAILURE;
        }

        $stub = $this->files->get(__DIR__ . "/../Stubs/domain/enum.stub");

        $content = str_replace(
            [
                '{{ module }}',
                '{{ enum }}',
            ],
            [$moduleName, $enumName],
            $stub
        );

        $directory = dirname($path);

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
        
        $this->files->put($path, $content);

        $this->components->twoColumnDetail("<fg=green>Enum</>", $path);
        $this->newLine();
        $this->info("✅ Enum [{$enumName}] created successfully in module [{$moduleName}].");

        return self::SUCCESS;
    }
}