<?php

namespace Foris\LaExtension\Console\Command;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class MakeFacade
 */
class MakeFacade extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:facade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new facade';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Facade';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('component')) {
            return __DIR__ . '/Stubs/FacadeComponent.stub';
        }
        return __DIR__ . '/Stubs/Facade.stub';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        return $this->replaceAccessor(parent::buildClass($name));
    }

    /**
     * Replace the component name for the given stub.
     *
     * @param      string  $stub   The stub
     *
     * @return     string
     */
    protected function replaceAccessor($stub)
    {
        $search = $replace = [];

        if ($component = $this->option('component')) {
            $search = ['NamespacedDummyComponent'];
            $replace[] = str_replace('/', '\\', $component);
        }

        if ($abstract = $this->option('abstract')) {
            $search[] = 'abstract';
            $replace[] = $abstract;
        }

        return str_replace($search, $replace, $stub);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['abstract', 'a', InputOption::VALUE_OPTIONAL, 'Generate a facade for the given abstract'],

            ['component', 'c', InputOption::VALUE_OPTIONAL, 'Generate a facade for the given component'],
        ];
    }
}
