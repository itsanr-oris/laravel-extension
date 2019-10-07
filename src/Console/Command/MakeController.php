<?php

namespace Foris\LaExtension\Console\Command;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Console\ControllerMakeCommand;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class for make repository commands.
 */
class MakeController extends ControllerMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * 原来的命令实例
     *
     * @var ControllerMakeCommand
     */
    protected $command;

    public function __construct(ControllerMakeCommand $command, Filesystem $files)
    {
        $this->command = $command;
        parent::__construct($files);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('resource')) {
            return __DIR__ . '/Stubs/CrudController.stub';
        }

        return parent::getStub();
    }

    /**
     * Build the class with the given name.
     *
     * @param  string $name
     * @return string
     * @throws InvalidArgumentException
     */
    protected function buildClass($name)
    {
        if ($this->option('resource')) {
            return $this->replaceServiceName(parent::buildClass($name));
        }
        return parent::buildClass($name);
    }

    /**
     * Replace the service name for the given stub.
     *
     * @param      string $stub The stub
     * @return     string
     * @throws InvalidArgumentException
     */
    protected function replaceServiceName($stub)
    {
        if ($service = $this->option('service')) {
            $fullClassName = str_replace('/', '\\', $service);
            $classNameArr  = explode('\\', $fullClassName);

            $stub = str_replace('NamespacedDummyService', $fullClassName, $stub);
            return str_replace('DummyService', end($classNameArr), $stub);
        }
        throw new InvalidArgumentException("请传入 service 参数");
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = [
            ['service', 's', InputOption::VALUE_OPTIONAL, 'resource manage service'],
        ];

        return array_merge(parent::getOptions(), $options);
    }
}
