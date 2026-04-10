<?php

namespace DevCortes\LaravelCleanArchitecture\Commands\Application;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeDtoCommand extends Command
{
    protected $signature = 'devcortes:dto {module : The name of the module} {name : The name of the dto}';

    protected $description = 'Create a new dto within a module';

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $name = Str::studly($this->argument('name'));

        $path = app_path("Src/Application/{$module}/DTOs/{$name}.php");

        if ($this->files->exists($path)) {
            $this->error("Factory already exists at {$path}");
            return self::FAILURE;
        }

        $stub = $this->files->get(__DIR__ . '/../../Stubs/application/dto.stub');

        $content = str_replace(['{{ module }}', '{{ name }}'], [$module, $name], $stub);

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