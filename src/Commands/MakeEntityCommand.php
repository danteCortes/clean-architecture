<?php

namespace DevCortes\LaravelCleanArchitecture\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeEntityCommand extends Command
{
    protected $signature = 'devcortes:entity {module : The name of the module} {name : The name of the entity}';

    protected $description = 'Create a new entity within a specified module';

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $entityName = Str::studly($this->argument('name'));
        $moduleName = Str::studly($this->argument('module'));

        $this->info("Creating entity: {$entityName} in module: {$moduleName}");

        $this->createEntity($entityName, $moduleName);

        $this->newLine();
        $this->info("✅ Entity [{$entityName}] created successfully in module [{$moduleName}].");

        return self::SUCCESS;
    }

    private function createEntity(string $entityName, string $moduleName): void
    {
        $path = app_path("Src/Domain/{$moduleName}/Entities/{$entityName}.php");
        $stub = $this->resolveStub('domain/entity');

        $content = $this->replacePlaceholders($stub, $moduleName, $entityName);

        $this->writeFile($path, $content);
        $this->components->twoColumnDetail("<fg=green>Entity</>", $path);
    }

    private function resolveStub(string $stub): string
    {
        // Allow published stubs to override package stubs
        $publishedPath = base_path("stubs/hexagonal/{$stub}.stub");

        $path = $this->files->exists($publishedPath)
            ? $publishedPath
            : __DIR__ . "/../Stubs/{$stub}.stub";

        return $this->files->get($path);
    }

    private function replacePlaceholders(
        string $stub,
        string $module,
        string $entity
    ): string {

        return str_replace(
            [
                '{{ module }}',
                '{{ entity }}',
            ],
            [$module, $entity],
            $stub
        );
    }

    private function writeFile(string $path, string $content): void
    {
        $directory = dirname($path);

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        $this->files->put($path, $content);
    }
}