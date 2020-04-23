<?php

namespace Foris\LaExtension\Tests\Traits\Repositories;

use Foris\LaExtension\Exceptions\BusinessException;
use Foris\LaExtension\Tests\TestCase;
use Foris\LaExtension\Tests\Traits\Models\DummyModel;
use Foris\LaExtension\Traits\Repositories\SelectOption;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mockery;

/**
 * Class SelectOptionTest
 */
class SelectOptionTest extends TestCase
{
    /**
     * 获取SelectOption实例
     *
     * @param bool $useTrait
     * @return Mockery\Mock|SelectOption
     */
    protected function mockSelectOption($useTrait = true)
    {
        $model = new DummyModel();

        if (!$useTrait) {
            $model = Mockery::mock(Model::class);
            $model->shouldReceive('getSelectOptionKeys')->andReturn(['id', 'name']);
        }

        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('select')->withAnyArgs()->andReturnSelf();
        $query->shouldReceive('get')->andReturn('success get select option info from database!');

        $mock = Mockery::mock(SelectOption::class)->makePartial();
        $mock->shouldReceive('model')->withAnyArgs()->andReturn($model);
        $mock->shouldReceive('query')->andReturn($query);

        return $mock;
    }

    /**
     * 测试是否支持获取选项信息操作
     */
    public function testHasSelectOptionOperation()
    {
        $this->assertTrue($this->mockSelectOption()->hasSelectOptionOperation());
        $this->assertFalse($this->mockSelectOption(false)->hasSelectOptionOperation());
    }

    /**
     * 测试正常获取选项信息
     *
     * @throws BusinessException
     */
    public function testGetSelectOptions()
    {
        $this->assertEquals('success get select option info from database!', $this->mockSelectOption()->selectOptions());
    }

    /**
     * 测试不支持获取选项信息操作条件下，获取选项信息是否正常
     *
     * @depends testHasSelectOptionOperation
     *
     * @throws BusinessException
     */
    public function testGetSelectOptionsWhileModelNotSupportOperation()
    {
        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('不支持选项信息操作操作!');
        $this->mockSelectOption(false)->selectOptions();
    }
}
