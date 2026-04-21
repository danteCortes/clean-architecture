<?php

namespace DevCortes\LaravelCleanArchitecture\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeFactoryCommand extends Command
{
    protected $signature = 'devcortes:factory {module : The name of the module} {name : The name of the factory}';

    protected $description = 'Create a new factory for a specified entity within a module';

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $name = Str::studly($this->argument('name'));

        $path = app_path("Src/Domain/{$module}/Factories/{$name}.php");

        if ($this->files->exists($path)) {
            $this->error("Factory already exists at {$path}");
            return self::FAILURE;
        }

        $stub = $this->files->get(__DIR__ . '/../Stubs/domain/factory.stub');

        $content = str_replace(['{{ module }}', '{{ name }}'], [$module, $name], $stub);

        $directory = dirname($path);

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        $this->files->put($path, $content);
        
        $this->components->twoColumnDetail("<fg=green>Factory</>", $path);
        $this->newLine();
        $this->info("✅ Factory [{$name}] created successfully in module [{$module}].");

        return self::SUCCESS;
    }

}