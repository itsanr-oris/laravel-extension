<?php

namespace Foris\LaExtension\Tests\Traits\Repositories;

use Foris\LaExtension\Exceptions\BusinessException;
use Foris\LaExtension\Tests\TestCase;
use Foris\LaExtension\Tests\Traits\Models\DummyModel;
use Foris\LaExtension\Traits\Models\StatusDefinition;
use Foris\LaExtension\Traits\Repositories\StatusOperation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mockery;

/**
 * Class StatusOperationTest
 */
class StatusOperationTest extends TestCase
{
    /**
     * 获取SelectOption实例
     *
     * @param bool $useTrait
     * @return Mockery\Mock|StatusOperation
     */
    protected function mockStatusOperation($useTrait = true)
    {
        $model = new DummyModel();

        if (!$useTrait) {
            $model = Mockery::mock(Model::class);
            $model->shouldReceive('getKeyName')->andReturn('id');
            $model->shouldReceive('getStatusKeyName')->andReturn('status');
        }

        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('whereIn')->with('id', [1,2])->andReturnSelf();
        $query->shouldReceive('update')
            ->with(['status' => StatusDefinition::STATUS_ENABLE])->andReturn('success enable resource [1, 2]!');
        $query->shouldReceive('update')
            ->with(['status' => StatusDefinition::STATUS_DISABLE])->andReturn('success disable resource [1, 2]!');

        $mock = Mockery::mock(StatusOperation::class)->makePartial();
        $mock->shouldReceive('model')->withAnyArgs()->andReturn($model);
        $mock->shouldReceive('query')->andReturn($query);

        return $mock;
    }

    /**
     * 测试是否支持更改资源状态信息操作
     */
    public function testHasStatusOperation()
    {
        $this->assertTrue($this->mockStatusOperation()->hasStatusOperation());
        $this->assertFalse($this->mockStatusOperation(false)->hasStatusOperation());
    }

    /**
     * 测试更改资源状态信息
     *
     * @throws \Foris\LaExtension\Exceptions\BusinessException
     */
    public function testChangeResourceStatus()
    {
        $this->assertEquals('success enable resource [1, 2]!', $this->mockStatusOperation()->enable([1,2]));
        $this->assertEquals('success disable resource [1, 2]!', $this->mockStatusOperation()->enable([1,2], false));
    }

    /**
     * 测试model不支持状态变更操作事，执行更改资源状态操作
     *
     * @throws BusinessException
     */
    public function testChangeResourceStatusWhileModelNotSupportOperation()
    {
        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('不支持状态变更操作!');
        $this->mockStatusOperation(false)->enable([1,2]);
    }
}
