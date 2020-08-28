<?php

namespace Foris\LaExtension\Repositories;

/**
 * Trait CacheProxyTrait
 */
trait CacheProxyTrait
{
    /**
     * 此处用于ide-helper生成文档，参数以及返回类型保持与CacheProxy一致
     *
     * @param bool  $enable
     * @param array $options
     * @return $this
     */
    public function cache($enable = true, $options = [])
    {
        return $this;
    }

    /**
     * 此处用于ide-helper生成文档，参数以及返回类型保持与CacheProxy一致
     *
     * @param array $options
     * @return $this
     */
    public function clearCache($options = [])
    {
        return $this;
    }

    /**
     * 此处用于ide-helper生成文档，参数以及返回类型保持与CacheProxy一致
     *
     * @param array $options
     * @return $this
     */
    public function refreshCache($options = [])
    {
        return $this;
    }

    /**
     * 此处用于ide-helper生成文档，参数以及返回类型保持与CacheProxy一致
     *
     * @param string $key
     * @return string
     */
    public function generateCacheKey(string $key)
    {
        return $key;
    }

    /**
     * 此处用于ide-helper生成文档，参数以及返回类型保持与CacheProxy一致
     *
     * @param string $tag
     * @return string
     */
    public function generateCacheTag(string $tag)
    {
        return $tag;
    }
}
