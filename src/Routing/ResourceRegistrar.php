<?php

namespace Foris\LaExtension\Routing;

/**
 * Class ResourceRegistrar
 */
class ResourceRegistrar extends \Illuminate\Routing\ResourceRegistrar
{
    /**
     * Route a resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array   $options
     * @return void
     */
    public function register($name, $controller, array $options = [])
    {
        if (!empty($options['with_extra'])) {
            $this->registerExtraResourceRoute($name, $controller, $options);
        }

        parent::register($name, $controller, $this->handleResourceRouterOptions($name, $options));
    }

    /**
     * 处理Resource路由选项信息
     *
     * @param       $name
     * @param array $options
     * @return array
     */
    protected function handleResourceRouterOptions($name, array $options = [])
    {
        $config = config('app-ext.resource_route.default', []);

        $resourceName = $options['resource_name'] ?? '';
        if (empty($resourceName)) {
            $parameters   = $options['parameters'] ?? [];
            $nameArr      = explode('/', $name);
            $resource     = end($nameArr);
            $resourceName = empty($parameters[$resource]) ? $resource : $parameters[$resource];
        }

        $options['names'] = $options['names'] ?? [];
        foreach ($config as $method => $routeName) {
            if (isset($options['names'][$method])) {
                continue;
            }
            $options['names'][$method] = str_replace('{resource_name}', $resourceName, $routeName);
        }

        return $options;
    }

    /**
     * 注册额外路由
     *
     * @param string $name
     * @param string $controller
     * @param array  $options
     */
    protected function registerExtraResourceRoute($name, $controller, array $options = [])
    {
        $config = config('app-ext.resource_route.extra', []);

        foreach ($config as $method => $itemConfig) {
            if (isset($options['except']) && in_array($method, $options['except'])) {
                continue;
            }

            if (isset($options['only']) && !in_array($method, $options['only'])) {
                continue;
            }

            // 组装路由
            $parameters = $options['parameters'] ?? [];
            $nameArr = explode('/', $name);
            $resource = end($nameArr);
            $resourceName = empty($parameters[$resource]) ? $resource : $parameters[$resource];
            $routeSuffix = $itemConfig['route_suffix'] ?? '';
            $route = $name . '/' . str_replace('{resource}', '{'. $resourceName .'}', $routeSuffix);

            // 组装路由名称
            $routeName = $resourceName . '.' . $method;

            if (isset($options['names'][$method])) {
                $routeName = $options['names'][$method];
            }

            if (isset($itemConfig['name']) && !isset($options['names'][$method])) {
                $resourceName = empty($options['resource_name']) ? $resourceName : $options['resource_name'];
                $routeName    = str_replace('{resource_name}', $resourceName, $itemConfig['name']);
            }

            $this->router->{$itemConfig['method']}($route , $controller . '@' . $method)->name($routeName);
        }
    }
}
