<?php

namespace DevCortes\LaravelCleanArchitecture\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeRepositoryCommand extends Command
{
    protected $signature = 'devcortes:repository {module : Module name} {name : Repository name}';

    protected $description = 'Create a new repository within a specified module';

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $repository = Str::studly($this->argument('name'));
        $module = Str::studly($this->argument('module'));

        $this->info("Creating repository: {$repository} in module: {$module}");
        
        $path = app_path("Src/Domain/{$module}/Repositories/{$repository}.php");

        $stub = $this->files->get(__DIR__ . "/../Stubs/domain/repository.stub");

        $content = str_replace(
            ['{{ module }}', '{{ repository }}'],
            [$module, $repository],
            $stub
        );

        $directory = dirname($path);

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        $this->files->put($path, $content);
        
        $this->components->twoColumnDetail("<fg=green>Entity</>", $path);
        $this->newLine();
        $this->info("✅ Repository [{$repository}] created successfully in module [{$module}].");

        return self::SUCCESS;
    }
}