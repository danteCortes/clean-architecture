<?php

namespace DevCortes\LaravelCleanArchitecture;

use Illuminate\Support\ServiceProvider;
use DevCortes\LaravelCleanArchitecture\Commands\MakeModuleCommand;
use DevCortes\LaravelCleanArchitecture\Commands\MakeValueObjectCommand;
use DevCortes\LaravelCleanArchitecture\Commands\MakeEntityCommand;
use DevCortes\LaravelCleanArchitecture\Commands\MakeRepositoryCommand;
use DevCortes\LaravelCleanArchitecture\Commands\MakeFactoryCommand;
use DevCortes\LaravelCleanArchitecture\Commands\MakeEnumCommand;

class DevCortesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeModuleCommand::class,
                MakeValueObjectCommand::class,
                MakeEntityCommand::class,
                MakeRepositoryCommand::class,
                MakeFactoryCommand::class,
                MakeEnumCommand::class,
                \DevCortes\LaravelCleanArchitecture\Commands\Application\MakeUseCaseCommand::class,
                \DevCortes\LaravelCleanArchitecture\Commands\Application\MakeDtoCommand::class,
                \DevCortes\LaravelCleanArchitecture\Commands\Application\MakeResponseCommand::class,
                \DevCortes\LaravelCleanArchitecture\Commands\Infrastructure\MakeModelCommand::class,
                \DevCortes\LaravelCleanArchitecture\Commands\Infrastructure\MakeImplementCommand::class,
                \DevCortes\LaravelCleanArchitecture\Commands\Infrastructure\MakeMapperCommand::class,
                \DevCortes\LaravelCleanArchitecture\Commands\Infrastructure\MakeRequestCommand::class,
                \DevCortes\LaravelCleanArchitecture\Commands\Infrastructure\MakeControllerCommand::class,
                \DevCortes\LaravelCleanArchitecture\Commands\Infrastructure\MakeServiceCommand::class,
                \DevCortes\LaravelCleanArchitecture\Commands\Infrastructure\MakeRouterCommand::class,
                \DevCortes\LaravelCleanArchitecture\Commands\Infrastructure\MakeProviderCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/Stubs' => base_path('stubs/hexagonal'),
            ], 'hexagonal-stubs');
        }
    }
}
