<?php

namespace Foris\LaExtension\Tests\Routing;

use Foris\LaExtension\Tests\TestCase;
use Illuminate\Support\Arr;

/**
 * Class RouterTest
 */
class RouterTest extends TestCase
{
    /**
     * Get uri array
     *
     * @return array
     */
    protected function uris()
    {
        return [
            'index' => [
                'methods' => ['GET', 'HEAD'],
                'uri' => 'resource',
                'name' => '查看资源列表',
            ],
            'create' => [
                'methods' => ['GET', 'HEAD'],
                'uri' => 'resource/create',
                'name' => '查看资源创建表单',
            ],
            'edit' => [
                'methods' => ['GET', 'HEAD'],
                'uri' => 'resource/{resource}/edit',
                'name' => '查看资源编辑表单',
            ],
            'store' => [
                'methods' => ['POST'],
                'uri' => 'resource',
                'name' => '创建资源信息',
            ],
            'update' => [
                'methods' => ['PUT', 'PATCH'],
                'uri' => 'resource/{resource}',
                'name' => '更新资源信息',
            ],
            'show' => [
                'methods' => ['GET', 'HEAD'],
                'uri' => 'resource/{resource}',
                'name' => '查看资源详情',
            ],
            'destroy' => [
                'methods' => ['DELETE'],
                'uri' => 'resource/{resource}',
                'name' => '删除资源信息',
            ],
            'batchDestroy' => [
                'methods' => ['DELETE'],
                'uri' => 'resource/delete/batch',
                'name' => '批量删除资源信息',
            ],
            'enable' => [
                'methods' => ['PUT'],
                'uri' => 'resource/{resource}/enable',
                'name' => '启用资源信息',
            ],
            'batchEnable' => [
                'methods' => ['PUT'],
                'uri' => 'resource/enable/batch',
                'name' => '批量启用资源信息',
            ],
            'disable' => [
                'methods' => ['PUT'],
                'uri' => 'resource/{resource}/disable',
                'name' => '禁用资源信息',
            ],
            'batchDisable' => [
                'methods' => ['PUT'],
                'uri' => 'resource/disable/batch',
                'name' => '批量禁用资源信息',
            ],
            'selectOptions' => [
                'methods' => ['GET', 'HEAD'],
                'uri' => 'resource/select_options',
                'name' => '查看资源选项信息'
            ],
        ];
    }

    /**
     * Test define resource route
     */
    public function testResource()
    {
        $options = [
            'resource_name' => '资源'
        ];
        $this->getRouter()->resource('resource', 'ResourceController', $options);

        $routes = $this->getRouter()->getRoutes();
        $this->assertCount(13, $routes);

        $uris = $this->uris();

        foreach ($routes as $route) {
            $method = $route->getActionMethod();
            if (empty($uris[$method])) {
                continue;
            }

            $this->assertEquals($uris[$method]['uri'], $route->uri);
            $this->assertEquals($uris[$method]['methods'], $route->methods);
            $this->assertEquals($uris[$method]['name'], $route->getName());
            unset($uris[$method]);
        }

        $this->assertEmpty($uris);
    }

    /**
     * Test define resource api route
     */
    public function testApiResource()
    {
        $options = [
            'resource_name' => '资源'
        ];
        $this->getRouter()->apiResource('resource', 'ResourceController', $options);

        $routes = $this->getRouter()->getRoutes();
        $this->assertCount(11, $routes);

        $uris = Arr::except($this->uris(), ['create', 'edit']);

        foreach ($routes as $route) {
            $method = $route->getActionMethod();
            if (empty($uris[$method])) {
                continue;
            }

            $this->assertEquals($uris[$method]['uri'], $route->uri);
            $this->assertEquals($uris[$method]['methods'], $route->methods);
            $this->assertEquals($uris[$method]['name'], $route->getName());
            unset($uris[$method]);
        }

        $this->assertEmpty($uris);
    }

    /**
     * Test define specified resource api route
     */
    public function testApiResourceWithOnlySpecifiedRoute()
    {
        $options = [
            'only' => ['index', 'batchDestroy']
        ];

        $this->getRouter()->resource('resource', 'ResourceController', $options);
        $routes = $this->getRouter()->getRoutes();
        $this->assertCount(2, $routes);

        $uris = Arr::only($this->uris(), ['index', 'batchDestroy']);
        foreach ($routes as $route) {
            unset($uris[$route->getActionMethod()]);
        }

        $this->assertEmpty($uris);
    }

    /**
     * Test define resource api route except specified route
     */
    public function testApiResourceExceptSpecifiedRoute()
    {
        $options = [
            'except' => ['index', 'batchDestroy']
        ];

        $this->getRouter()->resource('resource', 'ResourceController', $options);

        $routes = $this->getRouter()->getRoutes();
        $this->assertCount(11, $routes);

        $uris = Arr::except($this->uris(), ['index', 'batchDestroy']);

        foreach ($routes as $route) {
            unset($uris[$route->getActionMethod()]);
        }

        $this->assertEmpty($uris);
    }

    /**
     * Test define resource api route with custom route name
     */
    public function testCustomApiResourceRouteName()
    {
        $options = [
            'resource_name' => '资源',
            'only' => [
                'index', 'batchDestroy'
            ],
            'names' => [
                'index' => '自定义查看资源列表路由名称',
                'batchDestroy' => '自定义批量删除资源信息路由名称',
            ],
        ];

        $this->getRouter()->resource('resource', 'ResourceController', $options);

        $routes = $this->getRouter()->getRoutes();
        $this->assertCount(2, $routes);

        $uris = Arr::only($this->uris(), ['index', 'batchDestroy']);

        foreach ($routes as $route) {
            $method = $route->getActionMethod();
            $this->assertNotEquals($uris[$method]['name'], $route->getName());
            $this->assertEquals($options['names'][$method], $route->getName());
            unset($uris[$method]);
        }

        $this->assertEmpty($uris);
    }

    /**
     * Test extend api resource route
     */
    public function testExtendApiResourceRoute()
    {
        $extConfig = [
            'extendMethod' => [
                'method' => 'get',
                'route_suffix' => 'ext_method',
                'name' => '扩展{resource_name}操作',
            ],
        ];

        $config = array_merge($this->getConfig()->get('app-ext.resource_route.extra', []), $extConfig);
        $this->getConfig()->set('app-ext.resource_route.extra', $config);

        $options = [
            'resource_name' => '资源',
            'only' => ['extendMethod']
        ];
        $this->getRouter()->resource('resource', 'ResourceController', $options);

        $routes = $this->getRouter()->getRoutes();
        $this->assertCount(1, $routes);

        foreach ($routes as $route) {
            $this->assertEquals('extendMethod', $route->getActionMethod());
            $this->assertEquals('resource/ext_method', $route->uri);
            $this->assertEquals(['GET', 'HEAD'], $route->methods);
            $this->assertEquals('扩展资源操作', $route->getName());
        }
    }
}
