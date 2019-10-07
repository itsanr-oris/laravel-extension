<?php

namespace Foris\LaExtension\Traits;

use Illuminate\Foundation\AliasLoader;

/**
 * Trait FacadeAlias
 */
trait FacadeAlias
{
    /**
     * Get component facade alias name
     *
     * @return string
     */
    public static function aliasName()
    {
        $classSegments = explode('\\', static::class);
        unset($classSegments[0], $classSegments[1]);

        return array_shift($classSegments) . array_pop($classSegments);
    }

    /**
     * Alias component facade
     */
    public static function aliasFacade()
    {
        $alias = static::aliasName();
        !empty($alias) && AliasLoader::getInstance()->alias($alias, static::class);
    }
}