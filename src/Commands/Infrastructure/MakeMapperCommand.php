<?php

namespace DevCortes\LaravelCleanArchitecture\Commands\Infrastructure;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

final class MakeMapperCommand extends Command
{
    protected $signature = 'devcortes:mapper {module : The name of the module} {name : The name of the mapper}';

    protected $description = 'Create a new mapper within a module';

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $name = Str::studly($this->argument('name'));

        $path = app_path("Src\\Infrastructure\\{$module}\\Persistence\\Mappers\\{$name}.php");

        if ($this->files->exists($path)) {
            $this->error("Mapper already exists at {$path}");
            return self::FAILURE;
        }

        $stub = $this->files->get(__DIR__ . '/../../Stubs/infrastructure/mapper.stub');

        $content = str_replace(['{{ module }}', '{{ name }}'], [$module, $name], $stub);

        $directory = dirname($path);

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        $this->files->put($path, $content);
        
        $this->components->twoColumnDetail("<fg=green>Mapper</>", $path);
        $this->newLine();
        $this->info("✅ Mapper [{$name}] created successfully in module [{$module}].");

        return self::SUCCESS;
    }
}