<?php

namespace Foris\LaExtension\Console\Command;

use Foris\LaExtension\Services\CrudService;
use Foris\LaExtension\Services\Service;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class for make repository commands.
 */
class MakeService extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create service';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Service';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('repository')) {
            return __DIR__ . '/Stubs/CrudService.stub';
        }

        return __DIR__ . '/Stubs/Service.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\' . config('app-ext.file_path.services');
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        if ($this->option('facade')) {
            $name = $this->qualifyClass($this->getNameInput());
            $nameSegments = explode('\\', $name);
            $lastSegment = array_pop($nameSegments);
            array_push($nameSegments, 'Facade', $lastSegment);

            $this->call('make:facade', [
                'name' => implode('\\', $nameSegments),
                '--component' => $name,
            ]);
        }

        return parent::handle();
    }

    /**
     * Build the class with the given name.
     *
     * @param  string $name
     * @return string
     */
    protected function buildClass($name)
    {
        $search = $replace = [];
        $this->buildParentReplacements($search, $replace);
        $this->buildRepositoryReplacements($search, $replace);

        return str_replace($search, $replace, parent::buildClass($name));
    }

    /**
     * Build the replacements for parent repository.
     *
     * @param array $search
     * @param array $replace
     */
    public function buildParentReplacements(&$search = [], &$replace = [])
    {
        $fullParentClass = config('app-ext.parent_class.service', Service::class);
        if ($this->option('repository')) {
            $fullParentClass = config('app-ext.parent_class.crud_service', CrudService::class);
        }

        $search[] = 'FullParentClass';
        $search[] = 'ParentClass';

        $fullParentClass = str_replace('/', '\\', $fullParentClass);
        $parentClassArr = explode('\\', $fullParentClass);
        $replace[] = $fullParentClass;
        $replace[] = end($parentClassArr);
    }

    /**
     * Build the replacements for a resource model.
     *
     * @param array $search
     * @param array $replace
     */
    protected function buildRepositoryReplacements(&$search = [], &$replace = [])
    {
        if (!$repository = $this->option('repository')) {
            return ;
        }

        $search[] = 'NamespacedDummyRepository';
        $search[] = 'DummyRepository';

        $fullClassName = str_replace('/', '\\', $repository);
        $classNameArr  = explode('\\', $fullClassName);

        $replace[] = $fullClassName;
        $replace[] = end($classNameArr);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['facade', '', InputOption::VALUE_NONE, 'Generate facade for current service'],

            ['repository', 'r', InputOption::VALUE_REQUIRED, 'Generate a resource service for the given resource repository'],
        ];
    }
}