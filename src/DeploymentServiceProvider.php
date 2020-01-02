<?php

namespace RmhMedia\LaravelDeployment;

use App\Deployments\DeploymentCreator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class DeploymentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/Commands/MakeDeploymentCommand.php';
        include __DIR__.'/Commands/SanitizeDeployment.php';
        include __DIR__.'/Commands/ListDeployment.php';
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


//<?php
//
//// namespace rmh-media\Providers;
//
//use App\Deployments\DeploymentCreator;
//use Illuminate\Support\ServiceProvider;
//use Illuminate\Filesystem\Filesystem;
//use Illuminate\Console\Scheduling\Schedule;
//use Packages\RmhMedia\LaravelDeployment;
//
//class TestServiceProvider extends ServiceProvider
//{
//    /**
//     * The Artisan commands provided by your application.
//     *
//     * @var array
//     */
//    protected $commands = [
//        \Commands\MakeDeploymentCommand::class,
//        \Commands\SanitizeDeployment::class,
//        \Commands\ListDeployment::class,
//    ];
//
//    /**
//     * Define the application's command schedule.
//     *
//     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
//     * @return void
//     */
//    protected function schedule(Schedule $schedule)
//    {
//        // $schedule->command('inspire')
//        //          ->hourly();
//    }
//
//    /**
//     * Register the commands for the application.
//     *
//     * @return void
//     */
//
//    public function commands()
//    {
//        $this->load(__DIR__.'/Commands');
//
//        require base_path('routes/console.php');
//    }
//
//    /**
//     * Register the service provider.
//     *
//     * @return void
//     *e @SuppressWarnings(PHPMD.UnusedLocalVariable)
//     */
//    public function register()
//    {
//        $this->app->singleton(DeploymentCreator::class, function ($app) {
//            return new DeploymentCreator(app(Filesystem::class));
//        });
//    }
//
//    /**
//     * Get the services provided by the provider.
//     *
//     * @return array
//     */
//    public function provides()
//    {
//        return [DeploymentCreator::class];
//    }
//
//}
