<?php

namespace Foris\LaExtension\Tests;

use Foris\LaExtension\ServiceProvider;
use Foris\LaExtension\Tests\Mock\Stubs\Resource;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Routing\Router;

/**
 * Class TestCase
 */
abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use DatabaseMigrations;

    /**
     * 获取ServiceProvider
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    /**
     * 注册router
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
     * 设置运行环境
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $router = $app['router'];
        $this->addWebRoutes($router);
        $this->addApiRoutes($router);
    }

    /**
     * 添加web路由
     *
     * @param Router $router
     */
    protected function addWebRoutes($router)
    {
    }

    /**
     * 添加api路由
     *
     * @param $router
     */
    protected function addApiRoutes($router)
    {
    }

    /**
     * Set up
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(realpath(__DIR__ . '/Mock/migrations'));
        Resource::query()->create(['name' => 'resource a', 'desc' => 'resource a desc']);
        Resource::query()->create(['name' => 'resource b', 'desc' => 'resource b desc']);
    }
}
