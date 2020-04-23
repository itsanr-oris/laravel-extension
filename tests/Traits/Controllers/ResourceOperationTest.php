<?php

namespace Foris\LaExtension\Tests\Traits\Controllers;

use Foris\LaExtension\Http\Response;
use Foris\LaExtension\Services\CrudService;
use Foris\LaExtension\Tests\TestCase;
use Foris\LaExtension\Traits\Controllers\ResourceOperation;
use Mockery;

/**
 * Class ResourceOperationTest
 */
class ResourceOperationTest extends TestCase
{
    /**
     * set up
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->singleton('app-ext.response', function () {
            $mock = Mockery::mock(Response::class);
            $mock->shouldReceive('success')->andReturnArg(0);
            $mock->shouldReceive('failure')->andReturnArg(0);
            $mock->shouldReceive('notFound')->andReturnArg(0);
            return $mock;
        });
    }

    /**
     * 获取ResourceOperation实例
     *
     * @return Mockery\LegacyMockInterface|Mockery\MockInterface|ResourceOperation
     */
    public function mockResourceOperation()
    {
        $service = Mockery::mock(CrudService::class);
        $service->shouldReceive('list')->andReturn('list method have been called!');
        $service->shouldReceive('detail')->with(1)->andReturn('detail method have been called!');
        $service->shouldReceive('detail')->with(2)->andReturnNull();

        $service->shouldReceive('create')->with(['key' => true])->andReturn('create method have been called!');
        $service->shouldReceive('create')->with(['key' => false])->andReturnFalse();

        $service->shouldReceive('update')->with(1, ['key' => true])->andReturn('update method have been called!');
        $service->shouldReceive('update')->with(1, ['key' => false])->andReturnFalse();

        $service->shouldReceive('delete')->with(1)->andReturn('delete method have been called!');
        $service->shouldReceive('delete')->with(2)->andReturnFalse();

        $service->shouldReceive('batchDelete')->with([1])->andReturn('batch delete method have been called!');
        $service->shouldReceive('batchDelete')->with([2])->andReturnFalse();

        return Mockery::mock(ResourceOperation::class)
            ->makePartial()->shouldReceive('service')->andReturn($service)->getMock();
    }

    /**
     * 测试获取资源列表
     */
    public function testIndex()
    {
        $this->assertEquals('list method have been called!', $this->mockResourceOperation()->index());
    }

    /**
     * 测试查看资源详情
     */
    public function testShow()
    {
        $this->assertEquals('detail method have been called!', $this->mockResourceOperation()->show(1));
        $this->assertEquals('获取详情失败，找不到指定资源信息!', $this->mockResourceOperation()->show(2));
    }

    /**
     * 测试获取创建表单
     */
    public function testCreate()
    {
        $this->assertEquals(['form' => []], $this->mockResourceOperation()->create());
    }

    /**
     * 测试创建资源信息
     */
    public function testShore()
    {
        $request = request();
        $request['key'] = true;

        $this->assertEquals('create method have been called!', $this->mockResourceOperation()->store($request));

        $request['key'] = false;
        $this->assertEquals('创建资源信息失败，请稍后重新尝试!', $this->mockResourceOperation()->store($request));
    }

    /**
     * 测试获取更新表单信息
     */
    public function testEdit()
    {
        $data = [
            'form' => [],
            'data' => 'detail method have been called!',
        ];
        $this->assertEquals($data, $this->mockResourceOperation()->edit(1));
    }

    /**
     * 测试更新资源信息
     */
    public function testUpdate()
    {
        $request = request();
        $request['key'] = true;

        $this->assertEquals('update method have been called!', $this->mockResourceOperation()->update($request, 1));

        $request['key'] = false;
        $this->assertEquals('更新资源信息失败，请稍后重新尝试!', $this->mockResourceOperation()->update($request, 1));
    }

    /**
     * 测试删除资源信息
     *
     * @throws \Exception
     */
    public function testDestroy()
    {
        $this->assertEquals([], $this->mockResourceOperation()->destroy(1));
        $this->assertEquals('操作失败，请稍后重新尝试!', $this->mockResourceOperation()->destroy(2));
    }

    /**
     * 测试批量删除资源信息
     *
     * @throws \Throwable
     */
    public function testBatchDestroy()
    {
        $request = request();
        $request['ids'] = [1];

        $this->assertEquals([], $this->mockResourceOperation()->batchDestroy());

        $request['ids'] = [2];
        $this->assertEquals('操作失败，请稍后重新尝试!', $this->mockResourceOperation()->batchDestroy());
    }
}
