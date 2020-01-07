<?php

namespace RmhMedia\LaravelDeployment;

use Illuminate\Support\Str;
use Illuminate\Support\Composer;
use Illuminate\Console\Command;

class MakeDeploymentCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:deployment {version : The version of the deployment}
        {--path= : The location where the deployment file should be created}
        {--command=* : List of commands which should be executed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new deployment file';

    /**
     * The migration creator instance.
     *
     * @var \App\Deployments\DeploymentCreator
     */
    protected $creator;

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Create a new deployment install command instance.
     *
     * @param  \App\Deployments\DeploymentCreator  $creator
     * @param  \Illuminate\Support\Composer  $composer
     * @return void
     */
    public function __construct(DeploymentCreator $creator, Composer $composer)
    {
        parent::__construct();

        $this->creator = $creator;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->writeDeployment(
            trim($this->input->getArgument('version')),
            $this->input->getOption('command')
        );

        $this->composer->dumpAutoloads();
    }

    /**
     * Write the deployment file to disk.
     *
     * @param  string  $version
     * @param  array  $commands
     * @return string
     */
    protected function writeDeployment($version, $commands = [])
    {
        $file = $this->creator->create(
            $version, $this->getDeploymentPath(), $commands
        );

        $this->line("<info>Created Deployment:</info> {$file}");
    }

    /**
     * Get deployment path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getDeploymentPath()
    {
        $deploymentPath = '';
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            $deploymentPath = !$this->usingRealPath()
                ? $this->laravel->basePath() . '/' . $targetPath
                : $targetPath;
        } else {
            $deploymentPath = $this->laravel->databasePath() . DIRECTORY_SEPARATOR .'deployments';
        }
        if (!file_exists($deploymentPath)) {
            mkdir($deploymentPath);
        }
        return $deploymentPath;
    }

    /**
     * Determine if the given path(s) are pre-resolved "real" paths.
     *
     * @return bool
     */
    protected function usingRealPath()
    {
        return $this->input->hasOption('realpath') && $this->option('realpath');
    }

}
