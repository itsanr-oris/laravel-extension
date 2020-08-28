<?php

namespace Foris\LaExtension\Tests\Repositories;

use Foris\LaExtension\Exceptions\ErrorException;
use Foris\LaExtension\Repositories\CacheProxy;
use Foris\LaExtension\Repositories\CrudRepository;
use Foris\LaExtension\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Mockery\Mock;

/**
 * Class CacheProxyTest
 */
class CacheProxyTest extends TestCase
{
    /**
     * 初始化测试环境
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('app-ext.cache', [
            /**
             * 是否启用缓存
             */
            'enable' => true,

            /**
             * 缓存时间
             */
            'ttl' => 3600,
        ]);
    }

    /**
     * Get crud repository instance
     *
     * @return Mock|CrudRepository|CacheProxy
     */
    public function repository()
    {
        $repository = \Mockery::mock(CrudRepository::class)->makePartial();
        $repository->shouldReceive('detail')->once()->with(1)->andReturn('resource 1 detail');
        $repository->shouldReceive('detail')->andReturnNull();
        $repository->shouldReceive('create')->andReturnTrue();

        return new CacheProxy($repository);
    }

    /**
     * 测试缓存操作
     */
    public function testGetDetailFromCache()
    {
        $repository = $this->repository();

        // 读取并初始化缓存
        $this->assertEquals('resource 1 detail', $repository->cache()->detail(1));

        // 测试从缓存中读取信息
        $this->assertNull($repository->detail(1));
        $this->assertEquals('resource 1 detail', $repository->cache()->detail(1));

        // 测试清除缓存后再读取信息
        $repository->refreshCache()->detail(1);
        $this->assertNull($repository->cache()->detail(1));
    }

    /**
     * 测试通过create，update, save，delete，enable清除缓存
     */
    public function testClearCacheByDefaultClearCacheMethod()
    {
        $repository = $this->repository();
        $repository->cache()->detail(1);

        $repository->create([]);
        $this->assertNull($repository->cache()->detail(1));
    }

    /**
     * 测试通过方法以及传参生成缓存key
     *
     * @throws ErrorException
     */
    public function testGenerateMethodCacheKey()
    {
        $model = \Mockery::mock(Model::class);
        $model->shouldReceive('getKey')->andReturn('model_key');

        $method = 'detail';
        $params = [
            '', null, true, false, '中文字符串', $model
        ];

        $expected = 'detail.[0,empty_string][1,null][2,true][3,false][4,'. md5('中文字符串') . '][5,model_key]';

        $proxy = new CacheProxy(null);
        $this->assertEquals($expected, $proxy->generateMethodCacheKey($method, $params));
    }

    /**
     * 传入对象参数生成缓存key
     *
     * @throws ErrorException
     */
    public function testGenerateMethodCacheKeyWhilePassObjectParams()
    {
        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage('生成缓存键值失败，不支持的参数类型，请手动指定缓存key!');

        $proxy = new CacheProxy(null);
        $proxy->generateMethodCacheKey('detail', [$proxy]);
    }

    /**
     * 传入数组参数生成缓存key
     *
     * @throws ErrorException
     */
    public function testGenerateMethodCacheKeyWhilePassArrayParams()
    {
        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage('生成缓存键值失败，不支持的参数类型，请手动指定缓存key!');

        $proxy = new CacheProxy(null);
        $proxy->generateMethodCacheKey('detail', [[]]);
    }

    /**
     * 测试指定缓存tag与缓存key
     *
     * @throws ErrorException
     */
    public function testSpecCacheTagsAndKeys()
    {
        $repository = $this->repository();
        $detail = $repository->cache(['tags' => ['tag'], 'key' => 'key'])->detail(1);

        // 测试通过指定的tag和key获取缓存
        $this->assertNull($repository->cache()->detail(1));
        $this->assertEquals($detail, $repository->cache(['tags' => ['tag'], 'key' => 'key'])->detail(1));

        /**
         * 测试通过指定的tag和key清除缓存
         */
        $repository->clearCache(['tags' => ['tag'], 'key' => 'key']);
        $this->assertNull($repository->cache(['tags' => ['tag'], 'key' => 'key'])->detail(1));
    }

    /**
     * 测试清除某个方法组下的所有缓存
     *
     * @throws ErrorException
     */
    public function testClearCacheByMethod()
    {
        $repository = $this->repository();

        // 初始化缓存
        $repository->cache()->detail(1);

        // 测试清除整个方法下的所有缓存
        $repository->clearCache(['method' => 'detail']);
        $this->assertNull($repository->cache()->detail(1));
    }

    /**
     * 测试未设置Repository异常
     */
    public function testRepositoryNotSetException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('A repository has not been set.');

        $this->assertNull((new CacheProxy(null))->repository());
    }
}
