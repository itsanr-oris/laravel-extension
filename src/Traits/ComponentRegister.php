<?php

namespace Foris\LaExtension\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Trait ComponentRegister
 */
trait ComponentRegister
{
    /**
     * 服务注册
     */
    public static function register()
    {
        app()->bind(static::name(), function () {
            return new static();
        });
    }

    /**
     * 获取服务名称
     *
     * App\Services\TestService => test.service
     * App\Services\TestAbcService => test-abc.service
     * App\Services\Test\AbcService => test.abc.service
     *
     * @return     string  服务名称
     */
    public static function name()
    {
        $classSegment = explode('\\', static::class);
        $folderSegment =  Arr::except($classSegment, [0, 1, count($classSegment) - 1]);

        $nameSegment = [];
        foreach ($folderSegment as $folderName) {
            $nameSegment[] = static::segment($folderName);
        }

        $nameSegment[] = static::segment(end($classSegment), true);

        $prefix = config('app-ext.component.name_prefix', '');
        return empty($prefix) ? strtolower(implode('.', $nameSegment)) : $prefix . '.' . strtolower(implode('.', $nameSegment));
    }

    /**
     * Convert segment name to snake name.
     *
     * @param      $name
     * @param bool $last
     * @return string
     */
    protected static function segment($name, $last = false)
    {
        if (!$last) {
            return strtolower(Str::snake($name, '-'));
        }

        $snakeSegment = explode('-', Str::snake($name, '-'));
        $nameSegment[] = implode('-', Arr::except($snakeSegment, [count($snakeSegment) - 1]));
        $nameSegment[] = end($snakeSegment);

        return strtolower(implode('.', $nameSegment));
    }
}
