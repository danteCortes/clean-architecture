<?php

namespace DevCortes\LaravelCleanArchitecture\Commands\Infrastructure;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModelCommand extends Command
{
    protected $signature = 'devcortes:model {module : The name of the module} {name : The name of the model} {table=table : The name of the table}';

    protected $description = 'Create a new model within a module';

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $name = Str::studly($this->argument('name'));
        $table = $this->argument('table');

        $path = app_path("Src\\Infrastructure\\{$module}\\Persistence\\Models\\{$name}.php");

        if ($this->files->exists($path)) {
            $this->error("Factory already exists at {$path}");
            return self::FAILURE;
        }

        $stub = $this->files->get(__DIR__ . '/../../Stubs/infrastructure/model.stub');

        $content = str_replace(['{{ module }}', '{{ name }}', '{{ table }}'], [$module, $name, $table], $stub);

        $directory = dirname($path);

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        $this->files->put($path, $content);
        
        $this->components->twoColumnDetail("<fg=green>Entity</>", $path);
        $this->newLine();
        $this->info("✅ Response [{$name}] created successfully in module [{$module}].");

        return self::SUCCESS;
    }

}