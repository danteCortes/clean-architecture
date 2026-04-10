<?php

namespace DevCortes\LaravelCleanArchitecture\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    protected $signature = 'devcortes:module {name : The name of the module}';

    protected $description = 'Create a new hexagonal architecture module';

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $module    = Str::studly($this->argument('name'));
        $namespace = "App\\Src\\{$module}";
        $basePath  = app_path("Src/{$module}");
        $collection = Str::snake(Str::plural($module));

        $this->info("Creating module: {$module}");

        $this->createEntity($module, $namespace, $basePath);
        $this->createValueObject($module, $namespace, $basePath);
        $this->createUseCase($module, $namespace, $basePath);
        $this->createModel($module, $namespace, $basePath, $collection);
        $this->createRepositoryInterface($module, $namespace, $basePath);
        $this->createRepository($module, $namespace, $basePath);
        $this->createController($module, $namespace, $basePath);
        $this->createRequest($module, $namespace, $basePath);

        $this->newLine();
        $this->info("✅ Module [{$module}] created successfully.");
        $this->newLine();
        $this->warn("📌 Don't forget to bind the repository in a ServiceProvider:");
        $this->line("   \$this->app->bind(\\{$namespace}\\Infrastructure\\Persistence\\Repositories\\{$module}RepositoryInterface::class,");
        $this->line("       \\{$namespace}\\Infrastructure\\Persistence\\Repositories\\Mongo{$module}Repository::class);");

        return self::SUCCESS;
    }

    private function createEntity(string $module, string $namespace, string $basePath): void
    {
        $path = "{$basePath}/Domain/Entities/{$module}.php";
        $stub = $this->resolveStub('domain/entity');

        $content = $this->replacePlaceholders($stub, $module, $namespace);
        $this->writeFile($path, $content);
        $this->components->twoColumnDetail("<fg=green>Entity</>", $path);
    }

    private function createValueObject(string $module, string $namespace, string $basePath): void
    {
        $path = "{$basePath}/Domain/ValueObjects/{$module}Id.php";
        $stub = $this->resolveStub('domain/value-object');

        $content = $this->replacePlaceholders($stub, $module, $namespace);
        $this->writeFile($path, $content);
        $this->components->twoColumnDetail("<fg=green>Value Object</>", $path);
    }

    private function createUseCase(string $module, string $namespace, string $basePath): void
    {
        $path = "{$basePath}/Application/UseCases/Create{$module}UseCase.php";
        $stub = $this->resolveStub('application/use-case');

        $content = $this->replacePlaceholders($stub, $module, $namespace);
        $this->writeFile($path, $content);
        $this->components->twoColumnDetail("<fg=green>Use Case</>", $path);
    }

    private function createModel(string $module, string $namespace, string $basePath, string $collection): void
    {
        $path = "{$basePath}/Infrastructure/Persistence/Models/{$module}Model.php";
        $stub = $this->resolveStub('infrastructure/model');

        $content = $this->replacePlaceholders($stub, $module, $namespace, $collection);
        $this->writeFile($path, $content);
        $this->components->twoColumnDetail("<fg=green>Model</>", $path);
    }

    private function createRepositoryInterface(string $module, string $namespace, string $basePath): void
    {
        $path = "{$basePath}/Infrastructure/Persistence/Repositories/{$module}RepositoryInterface.php";
        $stub = $this->resolveStub('infrastructure/repository-interface');

        $content = $this->replacePlaceholders($stub, $module, $namespace);
        $this->writeFile($path, $content);
        $this->components->twoColumnDetail("<fg=green>Repository Interface</>", $path);
    }

    private function createRepository(string $module, string $namespace, string $basePath): void
    {
        $path = "{$basePath}/Infrastructure/Persistence/Repositories/Mongo{$module}Repository.php";
        $stub = $this->resolveStub('infrastructure/repository');

        $content = $this->replacePlaceholders($stub, $module, $namespace);
        $this->writeFile($path, $content);
        $this->components->twoColumnDetail("<fg=green>Repository</>", $path);
    }

    private function createController(string $module, string $namespace, string $basePath): void
    {
        $path = "{$basePath}/Infrastructure/Http/Controllers/{$module}Controller.php";
        $stub = $this->resolveStub('http/controller');

        $content = $this->replacePlaceholders($stub, $module, $namespace);
        $this->writeFile($path, $content);
        $this->components->twoColumnDetail("<fg=green>Controller</>", $path);
    }

    private function createRequest(string $module, string $namespace, string $basePath): void
    {
        $path = "{$basePath}/Infrastructure/Http/Requests/Create{$module}Request.php";
        $stub = $this->resolveStub('http/request');

        $content = $this->replacePlaceholders($stub, $module, $namespace);
        $this->writeFile($path, $content);
        $this->components->twoColumnDetail("<fg=green>Request</>", $path);
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
        string $namespace,
        string $collection = ''
    ): string {
        return str_replace(
            ['{{ namespace }}', '{{ module }}', '{{ collection }}'],
            [$namespace, $module, $collection],
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
