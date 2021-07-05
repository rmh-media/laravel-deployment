<?php

namespace RmhMedia\LaravelDeployment;

use RmhMedia\LaravelDeployment\Commands\DeploymentCreator;
use RmhMedia\LaravelDeployment\Commands\MakeDeploymentCommand;
use RmhMedia\LaravelDeployment\Commands\ExecDeployment;
use RmhMedia\LaravelDeployment\Commands\ListDeployment;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class DeploymentServiceProvider extends ServiceProvider
{
    protected $commands = [
        MakeDeploymentCommand::class,
        ExecDeployment::class,
        ListDeployment::class
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
            $this->publishMigrations();
        }
    }

    private function publishMigrations()
    {
        $path = $this->getMigrationsPath();
        $this->publishes([$path => database_path('migrations')], 'migrations');
    }

    private function getMigrationsPath()
    {
        return __DIR__ . '/Migrations/';
    }

    /**
     * Register the service provider.
     *
     * @return void
     *e @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function register()
    {
        $this->app->singleton(DeploymentCreator::class, function ($app) {
            return new DeploymentCreator(app(Filesystem::class));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [DeploymentCreator::class];
    }
}
