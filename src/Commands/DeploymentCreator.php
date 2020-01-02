<?php

namespace App\Deployments;

use Closure;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Filesystem\Filesystem;

class DeploymentCreator
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The registered post create hooks.
     *
     * @var array
     */
    protected $postCreate = [];

    /**
     * Prefix concatenated to filename
     *
     * @var string
     */
    protected $filePrefix = 'Deploy_';

    /**
     * Create a new deployment creator instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Create a new deployment at the given path.
     *
     * @param  string  $name
     * @param  string  $path
     * @param  array  $commands
     * @return string
     *
     * @throws \Exception
     */
    public function create($version, $path, $commands = [])
    {
        $this->ensureDeploymentDoesntAlreadyExist($version);

        // First we will get the stub file for the deployment, which serves as a type
        // of template for the deployment. Once we have those we will populate the
        // various place-holders, save the file, and run the post create event.
        $stub = $this->getStub();

        $this->files->put(
            $this->getPath($this->getClassName($version), $path),
            $this->populateStub($version, $stub, $commands)
        );

        $this->firePostCreateHooks();

        return $path;
    }

    /**
     * Ensure that a deployment with the given version doesn't already exist.
     *
     * @param  string  $version
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function ensureDeploymentDoesntAlreadyExist($version)
    {
        if (class_exists($className = $this->getClassName($version))) {
            throw new InvalidArgumentException("A {$className} class already exists.");
        }
    }

    /**
     * Get the deployment stub file.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->files->get($this->stubPath().'/deployment.stub');
    }

    /**
     * Populate the place-holders in the migration stub.
     *
     * @param  string  $version
     * @param  string  $stub
     * @param  array  $commands
     * @return string
     */
    protected function populateStub($version, $stub, $commands = [])
    {
        $stub = str_replace('DummyClass', $this->getClassName($version), $stub);

        if (! empty($commands)) {
            $finalCmdStr = '';
            foreach($commands as $command) {
                $split = explode(' ', $command);

                $cmd = array_shift($split);
                $argStr = array_shift($split);

                $str = '[\''. $cmd .'\',';
                $splitArgs = explode(',', $argStr);

                foreach($splitArgs as $arg) {
                    if (empty($arg)) {
                        continue;
                    }
                    $splitArg = explode('=', $arg);
                    if (count($splitArg) == 1) {
                        $str .= '\''. $splitArg[0] .'\',';
                    } else {
                        $val = $splitArg[1];
                        if ($val !== 'true' && $val !== 'false') {
                            $val = '\''. $val .'\'';
                        }
                        $str .= '\''. $splitArg[0] .'\' => '. $val .',';
                    }
                }

                $str .= ']';
                $finalCmdStr .= (strlen($finalCmdStr) == 0 ? '' : '            ') . $str .','. PHP_EOL;
            }
            $stub = str_replace('// Commands', substr($finalCmdStr, 0, -2), $stub);
        }

        return $stub;
    }

    /**
     * Get the class name of a migration name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getClassName($version)
    {
        return $this->filePrefix . str_replace(['.'], '_', Str::studly($version));
    }

    /**
     * Get the full path to the migration.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getPath($name, $path)
    {
        return $path.'/'.$this->getDatePrefix().'_'.$name.'.php';
    }

    /**
     * Fire the registered post create hooks.
     *
     * @param  string|null  $table
     * @return void
     */
    protected function firePostCreateHooks()
    {
        foreach ($this->postCreate as $callback) {
            call_user_func($callback);
        }
    }

    /**
     * Register a post deployment create hook.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function afterCreate(Closure $callback)
    {
        $this->postCreate[] = $callback;
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function stubPath()
    {
        return __DIR__.'/stubs';
    }

    /**
     * Get the filesystem instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFilesystem()
    {
        return $this->files;
    }

}
