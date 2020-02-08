<?php

namespace Foris\LaExtension\Console\Command;

use Foris\LaExtension\Repositories\CrudRepository;
use Foris\LaExtension\Repositories\Repository;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class for make repository commands.
 */
class MakeRepository extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('model')) {
            return __DIR__ . '/Stubs/CrudRepository.stub';
        }

        return __DIR__ . '/Stubs/Repository.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\' . config('app-ext.file_path.repositories');
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
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
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $search = $replace = [];
        $this->buildParentReplacements($search, $replace);
        $this->buildModelReplacements($search, $replace);

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
        $fullParentClass = config('app-ext.parent_class.repository', Repository::class);
        if ($this->option('model')) {
            $fullParentClass = config('app-ext.parent_class.crud_repository', CrudRepository::class);
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
    protected function buildModelReplacements(&$search = [], &$replace = [])
    {
        if (!$model = $this->option('model')) {
            return ;
        }

        $search[] = 'NamespacedDummyModel';
        $search[] = 'DummyModel';

        $fullClassName = str_replace('/', '\\', $model);
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
            ['facade', '', InputOption::VALUE_NONE, 'Generate facade for current repository'],

            ['model', 'm', InputOption::VALUE_REQUIRED, 'Generate a resource repository for the given model'],
        ];
    }
}
