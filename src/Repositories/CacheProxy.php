<?php /** @noinspection PhpUndefinedClassInspection */

namespace Foris\LaExtension\Repositories;

use Cache;
use Foris\LaExtension\Exceptions\ErrorException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Class CacheProxy
 */
class CacheProxy
{
    /**
     * 缓存启用标识
     *
     * @var bool
     */
    protected $enable = false;

    /**
     * 默认缓存配置
     *
     * @var array
     */
    protected $defaultOptions = [];

    /**
     * 缓存配置
     *
     * @var bool
     */
    protected $cacheOptions = [];

    /**
     * 是否刷新缓存
     *
     * @var bool
     */
    protected $refresh = false;

    /**
     * 缓存刷新选项
     *
     * @var bool
     */
    protected $refreshOptions = [];

    /**
     * Repository实例
     *
     * @var mixed
     */
    protected $repository;

    /**
     * 默认会清除缓存的操作，清除所有缓存
     *
     * @var array
     */
    protected $clearCacheMethod = ['create', 'update', 'save', 'delete', 'enable'];

    /**
     * CacheProxy constructor.
     *
     * @param       $repository
     * @param array $defaultOptions
     */
    public function __construct($repository, $defaultOptions= [])
    {
        $this->repository = $repository;
        $this->defaultOptions = $defaultOptions;
    }

    /**
     * 获取Repository实例
     *
     * @return Repository
     */
    public function repository()
    {
        if (!$this->repository) {
            throw new RuntimeException('A repository has not been set.');
        }
        return $this->repository;
    }

    /**
     * 获取缓存标识
     *
     * @param string $key
     * @return string
     */
    public function generateCacheKey(string $key)
    {
        return 'key.' . $this->repository()->name() . '.' . $key;
    }

    /**
     * 获取缓存标识
     *
     * @param string $tag
     * @return string
     */
    public function generateCacheTag(string $tag)
    {
        return 'tag.' . $this->repository()->name() . '.' . $tag;
    }

    /**
     * 获取默认缓存标识
     *
     * @return array
     */
    public function getDefaultCacheTags()
    {
        return [$this->repository()->name()];
    }

    /**
     * 设置缓存键标识
     *
     * @param $tags
     * @return $this
     */
    public function setCacheTags($tags)
    {
        if (!is_array($tags)) {
            $tags = (array) $tags;
        }

        foreach ($tags as $item) {
            $this->cacheOptions['tags'][] = $this->generateCacheTag($item);
        }

        return $this;
    }

    /**
     * 获取缓存标识信息
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge($this->getDefaultCacheTags(), $this->cacheOptions['tags'] ?? []);
    }

    /**
     * 设置缓存key
     *
     * 注：key自己也是一个tag，方便在指定key清除缓存的时候，能快速清除
     *
     * @param $key
     * @return CacheProxy
     */
    public function setCacheKey($key)
    {
        $this->cacheOptions['key'] = $this->generateCacheKey($key);
        $this->cacheOptions['tags'][] = $this->cacheOptions['key'];

        return $this;
    }

    /**
     * 获取缓存key
     *
     * @return mixed
     */
    public function getCacheKey()
    {
        return $this->cacheOptions['key'] ?? '';
    }

    /**
     * 设置缓存ttl
     *
     * @param int $ttl
     * @return $this
     */
    public function setCacheTtl($ttl = null)
    {
        $this->cacheOptions['ttl'] = empty($ttl) ? config('app-ext.cache.ttl', 3600) : $ttl;
        return $this;
    }

    /**
     * 获取缓存时间
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    public function getCacheTtl()
    {
        return empty($this->cacheOptions['ttl']) ? config('app-ext.cache.ttl', 3600) : $this->cacheOptions['ttl'];
    }

    /**
     * 重置缓存配置项
     *
     * @return $this
     */
    public function resetCacheOption()
    {
        $this->enable = false;
        $this->cacheOptions = [];

        $this->refresh = false;
        $this->refreshOptions = [];

        return $this;
    }

    /**
     * 设置缓存配置
     *
     * @param bool  $enable
     * @param array $options
     * @return $this
     */
    public function cache($enable = true, $options = [])
    {
        $this->resetCacheOption();
        $this->enable = is_bool($enable) ? $enable : true;

        if (is_array($enable)) {
            $options = array_merge($enable, $options);
        }

        if (!empty($options['tags'])) {
            $this->setCacheTags($options['tags']);
        }

        if (!empty($options['key'])) {
            $this->setCacheKey($options['key']);
        }

        $this->setCacheTtl($options['ttl'] ?? null);

        return $this;
    }

    /**
     * 清除缓存
     *
     * @param array $options
     * @return $this
     * @throws ErrorException
     */
    public function clearCache($options = [])
    {
        // 清空所有缓存
        if (empty($options)) {
            $this->clearAllCache();
        }

        // 清空方法缓存
        if (!empty($options['method'])) {
            $this->clearSpecMethodCache($options['method'], $options['method_args'] ?? []);
        }

        // 清空指定tag缓存
        if (!empty($options['tags'])) {
            $this->clearSpecTagsCache(Arr::wrap($options['tags']));
        }

        // 情况指定key缓存
        if (!empty($options['key'])) {
            $this->clearSpecKeysCache(Arr::wrap($options['key']));
        }

        // 增加调试信息
        Log::debug('清除缓存(clear)', ['repository' => get_class($this->repository()), 'options' => $options]);
        return $this;
    }

    /**
     * 清空所有缓存
     */
    public function clearAllCache()
    {
        Cache::tags($this->getDefaultCacheTags())->flush();
        $this->resetCacheOption();
    }

    /**
     * 清空指定方法的缓存
     *
     * @param       $method
     * @param array $args
     * @throws ErrorException
     */
    public function clearSpecMethodCache($method, $args = [])
    {
        $this->setCacheTags($method);

        if (empty($args)) {
            Cache::tags($this->getCacheTags())->flush();
        } else {
            $keys = [$this->generateMethodCacheKey($method, $args)];
            $this->clearSpecKeysCache($keys);
        }
        $this->resetCacheOption();
    }

    /**
     * 清空指定tags,keys缓存
     *
     * @param array $tags
     */
    public function clearSpecTagsCache($tags = [])
    {
        foreach ($tags as $tag) {
            Cache::tags([$this->generateCacheTag($tag)])->flush();
        }

        $this->resetCacheOption();
    }

    /**
     * 清空tag缓存
     *
     * @param array $keys
     */
    public function clearSpecKeysCache($keys = [])
    {
        foreach ($keys as $key) {
            Cache::tags([$this->generateCacheKey($key)])->flush();
        }

        $this->resetCacheOption();
    }

    /**
     * 刷新缓存
     *
     * @param array $options
     * @return $this
     */
    public function refreshCache($options = [])
    {
        $this->refresh = true;
        $this->refreshOptions = $options;
        return $this;
    }

    /**
     * 生成method对应缓存key
     *
     * @param $method
     * @param $args
     * @return string
     * @throws ErrorException
     */
    public function generateMethodCacheKey($method, $args)
    {
        $str = '';
        foreach ($args as $key => $value) {
            // 如果是Eloquent模型实例，尝试获取主键作为唯一标识
            if ($value instanceof Model) {
                $pk = $value->getKey();
                if (!empty($pk)) {
                    $value = $pk;
                }
            }

            // 数组参数、对象参数内部可能结构复杂，不同的排版方式可能会导致生成的key不一致，暂时不支持
            if (is_array($value) || is_object($value)) {
                throw new ErrorException('生成缓存键值失败，不支持的参数类型，请手动指定缓存key!');
            }

            if (is_null($value)) {
                $value = 'null';
            }

            if ($value === '') {
                $value = 'empty_string';
            }

            if ($value === true) {
                $value = 'true';
            }

            if ($value === false) {
                $value = 'false';
            }

            if (is_string($value)) {
                // 中文字符串需要进行md5，避免memcache key值异常
                if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $value)) {
                    $value = md5($value);
                }

                // url encode一下避免特殊字符
                $value = urlencode($value);
            }

            $str .= '[' . $key . ',' . $value . ']';
        }

        // 长度大于180的字符串，做MD5处理，避免key值过大，超出限制
        return strlen($str) > 180 ? $method . '.' . md5($str) : $method . '.' . $str;
    }

    /**
     * 执行方法
     *
     * @param $method
     * @param $args
     * @return mixed
     * @throws ErrorException
     */
    public function __call($method, $args)
    {
        return $this->afterMethodExecute($method, $this->execute($method, $args));
    }

    /**
     * 执行方法
     *
     * @param $method
     * @param $args
     * @return mixed|null
     * @throws ErrorException
     */
    protected function execute($method, $args)
    {
        if ($this->refresh) {
            $this->executeCacheRefresh($method, $args);
            return null;
        }

        if (!config('app-ext.cache.enable', false) || !$this->enable) {
            return $this->repository()->$method(...$args);
        }

        // 当前方法作为一个tag
        $this->setCacheTags($method);

        $key = $this->getCacheKey();
        if (empty($key)) {
            $key = $this->generateMethodCacheKey($method, $args);
            $key = $this->setCacheKey($key)->getCacheKey();
        }

        $tags = $this->getCacheTags();

        if (Cache::tags($tags)->has($key)) {
            $this->resetCacheOption();
            $cache = Cache::tags($tags)->get($key);

            // 增加调试信息
            $repository = get_class($this->repository());
            Log::debug('读取缓存', [
                'repository' => $repository, 'method' => $method, 'args' => $args, 'tags' => $tags, 'cache_content' => $cache
            ]);

            return $cache;
        }

        $ttl = $this->getCacheTtl();
        $result = $this->repository()->$method(...$args);
        Cache::tags($tags)->put($key, $result, now()->addSeconds($ttl));

        // 增加调试信息
        $repository = get_class($this->repository());
        Log::debug('写入缓存', [
            'repository' => $repository, 'method' => $method, 'args' => $args, 'tags' => $tags, 'cache_content' => $result
        ]);

        $this->resetCacheOption();
        return Cache::tags($tags)->get($key);
    }

    /**
     * 刷新缓存
     *
     * @param $method
     * @param $args
     * @throws ErrorException
     */
    protected function executeCacheRefresh($method, $args)
    {
        // 增加调试信息
        Log::debug('清除缓存(refresh)', [
            'repository' => get_class($this->repository()), 'method' => $method, 'args' => $args
        ]);

        $this->clearCache(['method' => $method, 'method_args' => $args]);
        $this->resetCacheOption();
    }

    /**
     * 方法执行完毕之后的操作
     *
     * @param $method
     * @param $result
     * @return mixed
     * @throws ErrorException
     */
    protected function afterMethodExecute($method, $result)
    {
        if (in_array($method, $this->clearCacheMethod)) {
            $this->clearCache();
        }

        return $result;
    }
}
