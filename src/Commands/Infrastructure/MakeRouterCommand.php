<?php

namespace DevCortes\LaravelCleanArchitecture\Commands\Infrastructure;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeRouterCommand extends Command
{
    protected $signature = 'devcortes:router {module : The name of the module} {name : The name of the router}';

    protected $description = 'Create a new router within a module';

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $name = $this->argument('name');

        $path = app_path("Src\\Infrastructure\\{$module}\\Routes\\{$name}.php");

        if ($this->files->exists($path)) {
            $this->error("Router already exists at {$path}");
            return self::FAILURE;
        }

        $stub = $this->files->get(__DIR__ . '/../../Stubs/infrastructure/router.stub');

        $content = str_replace(['{{ module }}', '{{ route }}'], [$module, Str::kebab($module)], $stub);

        $directory = dirname($path);

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        $this->files->put($path, $content);
        
        $this->components->twoColumnDetail("<fg=green>Router</>", $path);
        $this->newLine();
        $this->info("✅ Router [{$name}] created successfully in module [{$module}].");

        return self::SUCCESS;
    }

}