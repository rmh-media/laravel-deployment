<?php

namespace RmhMedia\LaravelDeployment\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ListDeployment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:list
        {--undone : Show only not executed deployments}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The command outputs a list of exectued deployments';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $fs;

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
        $deploymentsDone = \DB::table('deployments')->get()->pluck('deployment');

        $undone = $this->option('undone');
        if ($undone) {
            $files = $this->fs->files($this->getDeploymentPath());

            foreach($files as $deploymentFile) {
                $fileName = $deploymentFile->getBasename('.php');
                if (!$deploymentsDone->contains($fileName)) {
                    $this->line($fileName);
                }
            }
        } else {
            foreach($deploymentsDone as $deployment) {
                $this->line($deployment);
            }
        }
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
