<?php

namespace Foris\LaExtension\Routing;

/**
 * Class Router
 */
class Router extends \Illuminate\Routing\Router
{
    /**
     * Route an API resource to a controller.
     *
     * @param string $name
     * @param string $controller
     * @param array  $options
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    public function apiResource($name, $controller, array $options = [])
    {
        $extra = array_keys(config('app-ext.resource_route.extra', []));

        $options = array_merge([
            'only' => array_merge(['index', 'show', 'store', 'update', 'destroy'], $extra)
        ], $options);

        return parent::apiResource($name, $controller, $options);
    }
}
