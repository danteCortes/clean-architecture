<?php

namespace DevCortes\LaravelCleanArchitecture\Commands\Application;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeUseCaseCommand extends Command
{
    protected $signature = 'devcortes:use-case {module : The name of the module} {name : The name of the use case}';

    protected $description = 'Create a new use case within a module';

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $rawName = $this->argument('name');

        // Normaliza separadores: convierte / y \ en \, y elimina los del inicio
        $normalized = ltrim(str_replace('/', '\\', $rawName), '\\');

        // Separa el subfolder del nombre de la clase
        $parts     = explode('\\', $normalized);
        $className = Str::studly(array_pop($parts));
        $subFolder = implode('\\', array_map([Str::class, 'studly'], $parts));

        // Namespace y path
        $namespaceSuffix = $subFolder ? "\\{$subFolder}" : '';
        $pathSuffix      = $subFolder ? str_replace('\\', DIRECTORY_SEPARATOR, $subFolder) . DIRECTORY_SEPARATOR : '';

        $namespace = "App\\Src\\Application\\{$module}\\UseCases{$namespaceSuffix}";
        $path      = app_path("Src/Application/{$module}/UseCases/{$pathSuffix}{$className}.php");

        if ($this->files->exists($path)) {
            $this->error("Use case already exists at {$path}");
            return self::FAILURE;
        }

        $stub = $this->files->get(__DIR__ . '/../../Stubs/application/use-case.stub');

        $content = str_replace(
            ['{{ namespace }}', '{{ module }}', '{{ name }}'],
            [$namespace,        $module,         $className],
            $stub
        );

        $directory = dirname($path);

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        $this->files->put($path, $content);
        
        $this->components->twoColumnDetail("<fg=green>Entity</>", $path);
        $this->newLine();
        $this->info("✅ Use case [{$className}] created successfully in module [{$module}].");

        return self::SUCCESS;
    }

}