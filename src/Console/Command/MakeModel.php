<?php

namespace Foris\LaExtension\Console\Command;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\ModelMakeCommand;

/**
 * Class for make repository commands.
 */
class MakeModel extends ModelMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent model class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * 原来的命令实例
     *
     * @var ModelMakeCommand
     */
    protected $command;

    public function __construct(ModelMakeCommand $command, Filesystem $files)
    {
        $this->command = $command;
        parent::__construct($files);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (parent::handle() === false && ! $this->option('force')) {
            return;
        }

        if ($this->option('resource')) {
            $this->makeCrudRepository();
            $this->makeCrudService();
        }
    }

    /**
     * Create a controller for the model.
     *
     * @return void
     */
    protected function createController()
    {
        $options = [
            'name' => $this->getNameInput() . 'Controller',
        ];

        if ($this->option('resource')) {
            $options = array_merge($options, [
                '--service' => sprintf(
                    'App/%s/%sService', config('app-ext.file_path.services'), $this->getNameInput()
                ),
                '--resource' => true,
            ]);
        }

        $this->call('make:controller', $options);
    }

    /**
     * Makes a crud repository.
     */
    public function makeCrudRepository()
    {
        $this->call('make:repository', [
            'name' => $this->getNameInput() . 'Repository',
            '--model' => $this->qualifyClass($this->getNameInput()),
            '--facade' => true,
        ]);
    }

    /**
     * Makes a crud service.
     */
    public function makeCrudService()
    {
        $this->call('make:service', [
            'name' => $this->getNameInput() . 'Service',
            '--repository' => sprintf(
                'App/%s/%sRepository', config('app-ext.file_path.repositories'), $this->getNameInput()
            ),
            '--facade' => true,
        ]);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\' .  config('app-ext.file_path.models');
    }
}