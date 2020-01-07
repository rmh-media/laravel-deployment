<?php

namespace RmhMedia\LaravelDeployment;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ExecDeployment extends Command
{
    /**php
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:exec
        {--all : Execute all available deployments}
        {--done : Mark all available deployments as done}
        {--force : Force execution of already ran deployment}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The command executes maintenance and sanitizing tasks after successful code deployment';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * Prefix concatenated to filename
     *
     * @var string
     */
    protected $classPrefix = 'Deploy_';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Sanitizing deployment...');

        $deploymentsDone = \DB::table('deployments')->get()->pluck('deployment');

        $files = $this->fs->files($this->getDeploymentPath());
        print_r($files);
        $all = $this->option('all');
        $markAsDone = $this->option('done');
        $force = $this->option('force');

        if (count($files) == 0) {
            $this->info('Nothing to sanitize');
            return;
        }

        if (!$all) {
            $lastDeployment = $files[count($files) - 1];
            if(is_string($lastDeployment)) {
                $fileName = basename($lastDeployment, '.php');
            } else {
                $fileName = $lastDeployment->getBasename('.php');
            }


            if (!$deploymentsDone->contains($fileName)) {
                $this->info('Execute '. $fileName);
                if ($markAsDone || $this->execDeployment($fileName)) {
                    \DB::table('deployments')->insert(['deployment' => $fileName]);
                }
            } else if ($force && !$markAsDone) {
                $this->info('Executing "'. $fileName .'" again...');
                $this->execDeployment($fileName);
            } else {
                $this->info('Last deployment "'. $fileName .'" was already executed');
            }
        } else {
            foreach($files as $deploymentFile) {
                $fileName = $deploymentFile->getBasename('.php');

                if (!$deploymentsDone->contains($fileName)) {
                    $this->info('Execute '. $fileName);
                    if ($markAsDone || $this->execDeployment($fileName)) {
                        \DB::table('deployments')->insert(['deployment' => $fileName]);
                    }
                } else if ($force && !$markAsDone) {
                    $this->info('Executing "'. $fileName .'" again...');
                    $this->execDeployment($fileName);
                } else {
                    $this->info('Deployment "'. $fileName .'" was already executed');
                }
            }
        }
    }

    /**
     * Execute deployment
     *
     * @param object $deployment
     * @return boolean Succeeded
     */
    protected function execDeployment($fileName)
    {
        preg_match('/'. $this->classPrefix .'(.*)/', $fileName, $matches);

        if (!empty($matches)) {
            $cls = $matches[0];
            $deployment = new $cls;
            $ret = $deployment->run();
            if ($ret === false) {
                return;
            }

            $commands = $deployment->commands();
            foreach($commands as $command) {
                $cmd = array_shift($command);
                $this->info('Running command "'. $cmd .'" ...');
                $args = [];
                if (!empty($command)) {
                    $args = $command;
                }

                $this->call($cmd, $args);
            }
        }

        return true;
    }

    /**
     * Get deployments path.
     *
     * @return string
     */
    protected function getDeploymentPath()
    {
        return $this->laravel->databasePath() . DIRECTORY_SEPARATOR .'deployments';
    }

}
