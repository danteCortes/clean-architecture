<?php

namespace DevCortes\LaravelCleanArchitecture\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeValueObjectCommand extends Command
{
    protected $signature = 'devcortes:value-object {module : The name of the module} {name : The name of the value object}';

    protected $description = 'Create a new value object for a specific module';

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $module    = Str::studly($this->argument('module'));
        $name    = Str::studly($this->argument('name'));
        $filePath  = app_path("Src/Domain/{$module}/ValueObjects/{$name}.php");

        $this->info("Creating value object: {$name}");
        
        $publishedPath = base_path("stubs/hexagonal/domain/value-object.stub");

        $path = $this->files->exists($publishedPath)
            ? $publishedPath
            : __DIR__ . "/../Stubs/domain/value-object.stub";

        $stub = $this->files->get($path);

        $content = str_replace(
            ['{{ module }}', '{{ name }}'],
            [$module, $name],
            $stub
        );
        
        $directory = dirname($filePath);

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        $this->files->put($filePath, $content);
        
        $this->components->twoColumnDetail("<fg=green>Value Object</>", $filePath);

        $this->newLine();
        $this->info("✅ Value object [{$name}] created successfully.");
        $this->newLine();
        
        return self::SUCCESS;
    }

}