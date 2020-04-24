<?php /** @noinspection PhpIncludeInspection */

namespace Foris\LaExtension\Tests;

use Foris\LaExtension\Component;
use Foris\LaExtension\ServiceProvider;
use Foris\LaExtension\Tests\Stubs\Models\Resource;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Routing\Router;
use org\bovigo\vfs\vfsStream;
use TiMacDonald\Log\LogFake;
use Illuminate\Support\Facades\Log;

/**
 * Class TestCase
 */
abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use DatabaseMigrations;

    /**
     * vfsStreamDirectory instance
     *
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    protected $vfs;

    /**
     * Init vfs instance
     *
     * @return \org\bovigo\vfs\vfsStreamDirectory
     */
    protected function initVfs()
    {
        if (empty($this->vfs)) {
            $base = vfsStream::setup('laravel');
            $this->vfs = vfsStream::copyFromFileSystem(parent::getBasePath(), $base);

            $componentStub = __DIR__ . '/Stubs/Components/AutoRegisterComponent.stub';
            $facadeStub = __DIR__ . '/Stubs/Components/Facade/AutoRegisterComponent.stub';
            $controllerStub = __DIR__ . '/Stubs/Controllers/Controller.stub';

            $structure = [
                'Components' => [
                    'Module' => [
                        'AutoRegisterComponent.php' => file_get_contents($componentStub),
                        'Facade' => [
                            'AutoRegisterComponent.php' => file_get_contents($facadeStub),
                        ]
                    ],
                ],
                'Http' => [
                    'Controllers' => [
                        'Controller.php' => file_get_contents($controllerStub)
                    ]
                ],
            ];

            vfsStream::create($structure, $this->vfs->getChild('app'));

            require_once $this->vfs->url() . '/app/Components/Module/AutoRegisterComponent.php';
            require_once $this->vfs->url() . '/app/Components/Module/Facade/AutoRegisterComponent.php';
            require_once $this->vfs->url() . '/app/Http/Controllers/Controller.php';
        }
dd(Component::scanFiles($this->vfs->url()));
        return $this->vfs;
    }

    /**
     * Get vfs instance
     *
     * @return \org\bovigo\vfs\vfsStreamDirectory
     */
    protected function vfs()
    {
        return empty($this->vfs) ? $this->initVfs() : $this->vfs;
    }

    /**
     * Get application base path
     *
     * @return string
     */
    protected function getBasePath()
    {
        return $this->vfs()->url();
    }

    /**
     * Get package providers
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        # add custom app-ext config before package provider load
        $config = require __DIR__ . '/Stubs/config/app-ext.php';
        $app['config']->set('app-ext', array_merge($app['config']->get('app-ext', []), $config));

        return [ServiceProvider::class];
    }

    /**
     * Register router
     *
     * @return \Illuminate\Foundation\Application
     */
    protected function resolveApplication()
    {
        $app = parent::resolveApplication();
        $app->singleton('router', \Foris\LaExtension\Routing\Router::class);
        return $app;
    }

    /**
     * Get router instance
     *
     * @return Router
     */
    protected function getRouter()
    {
        return $this->app['router'];
    }

    /**
     * Get config instance
     *
     * @return \Illuminate\Config\Repository
     */
    protected function getConfig()
    {
        return $this->app['config'];
    }

    /**
     * Set up
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(realpath(__DIR__ . '/Stubs/migrations'));
        Resource::query()->create(['name' => 'resource a', 'desc' => 'resource a desc']);
        Resource::query()->create(['name' => 'resource b', 'desc' => 'resource b desc']);

        Log::swap(new LogFake());
        putenv('APP_DEBUG=true');
    }

    /**
     * 获取loger
     *
     * @return Log|LogFake
     */
    public function getLogger()
    {
        return Log::getFacadeRoot();
    }
}
