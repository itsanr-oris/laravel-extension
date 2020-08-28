<?php

namespace Foris\LaExtension\Repositories;

use Foris\LaExtension\Traits\ComponentRegister;

/**
 * Class Repository
 */
abstract class Repository
{
    use ComponentRegister, CacheProxyTrait{
        register as traitRegister;
    }

    /**
     * 修改Repository注册方法，返回缓存代理实例
     */
    public static function register()
    {
        app()->bind(static::name(), function () {
            return in_array(env('APP_ENV'), ['local', 'develop']) ? new static() : new CacheProxy(new static());
        });
    }
}
