<?php

namespace Foris\LaExtension\Http\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Class Response
 */
class Response extends Facade
{
    /**
     * 获得组件注册名称
     *
     * @return string
     * @codeCoverageIgnore
     */
    protected static function getFacadeAccessor()
    {
        return 'app-ext.response';
    }
}
