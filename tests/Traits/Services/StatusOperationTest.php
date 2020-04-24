<?php

namespace Foris\LaExtension\Tests\Traits\Services;

use Foris\LaExtension\Repositories\Repository;
use Foris\LaExtension\Tests\TestCase;
use Foris\LaExtension\Traits\Services\StatusOperation;
use Mockery;

/**
 * Class StatusOperationTest
 */
class StatusOperationTest extends TestCase
{
    /**
     * 获取SelectOption实例
     *
     * @return Mockery\Mock|StatusOperation
     */
    protected function mockSelectOption()
    {
        $repository = Mockery::mock(Repository::class);
        $repository->shouldReceive('enable')->with(1, false)->andReturn('success disable resource [1]!');

        $mock = Mockery::mock(StatusOperation::class)->makePartial();
        $mock->shouldReceive('repository')->andReturn($repository);
        return $mock;
    }

    /**
     * 测试更新资源状态
     *
     * @throws \Foris\LaExtension\Exceptions\BusinessException
     */
    public function testChangeResourceStatus()
    {
        $this->assertEquals('success disable resource [1]!', $this->mockSelectOption()->enable(1, false));
    }
}
