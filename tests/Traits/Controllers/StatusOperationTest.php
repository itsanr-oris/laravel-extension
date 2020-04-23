<?php

namespace Foris\LaExtension\Tests\Traits\Controllers;

use Foris\LaExtension\Http\Response;
use Foris\LaExtension\Services\CrudService;
use Foris\LaExtension\Tests\TestCase;
use Foris\LaExtension\Traits\Controllers\StatusOperation;
use Mockery;

/**
 * Class StatusOperationTest
 */
class StatusOperationTest extends TestCase
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
            return $mock;
        });
    }

    /**
     * 获取ResourceOperation实例
     *
     * @return Mockery\LegacyMockInterface|Mockery\MockInterface|StatusOperation
     */
    public function mockStatusOperation()
    {
        $service = Mockery::mock(CrudService::class);
        $service->shouldReceive('enable')->with(1)->andReturn('resource 1 have been enable!');
        $service->shouldReceive('enable')->with(1, false)->andReturn('resource 1 have been disable!');
        $service->shouldReceive('enable')->withArgs([[1,2]])->andReturnTrue();
        $service->shouldReceive('enable')->withArgs([[3,4]])->andReturnFalse();
        $service->shouldReceive('enable')->withArgs([[1,2], false])->andReturnTrue();
        $service->shouldReceive('enable')->withArgs([[3,4], false])->andReturnFalse();

        return Mockery::mock(StatusOperation::class)
            ->makePartial()->shouldReceive('service')->andReturn($service)->getMock();
    }

    /**
     * 测试启用资源信息
     *
     * @throws \Foris\LaExtension\Exceptions\BusinessException
     */
    public function testEnable()
    {
        $this->assertEquals('resource 1 have been enable!', $this->mockStatusOperation()->enable(1));
    }

    /**
     * 测试禁用资源信息
     *
     * @throws \Foris\LaExtension\Exceptions\BusinessException
     */
    public function testDisable()
    {
        $this->assertEquals('resource 1 have been disable!', $this->mockStatusOperation()->disable(1));
    }

    /**
     * 测试批量启用资源信息
     *
     * @throws \Foris\LaExtension\Exceptions\BusinessException
     */
    public function testBatchEnable()
    {
        $request = request();
        $request['ids'] = [1,2];

        $this->assertEquals([], $this->mockStatusOperation()->batchEnable());

        $request['ids'] = [3,4];
        $this->assertEquals('操作失败，请稍后重新尝试!', $this->mockStatusOperation()->batchEnable());
    }

    /**
     * 测试批量禁用资源信息
     *
     * @throws \Foris\LaExtension\Exceptions\BusinessException
     */
    public function testBatchDisable()
    {
        $request = request();
        $request['ids'] = [1,2];

        $this->assertEquals([], $this->mockStatusOperation()->batchDisable());

        $request['ids'] = [3,4];
        $this->assertEquals('操作失败，请稍后重新尝试!', $this->mockStatusOperation()->batchDisable());
    }
}
