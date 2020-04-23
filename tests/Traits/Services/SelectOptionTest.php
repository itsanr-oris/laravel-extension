<?php

namespace Foris\LaExtension\Tests\Traits\Services;

use Foris\LaExtension\Repositories\Repository;
use Foris\LaExtension\Tests\TestCase;
use Foris\LaExtension\Traits\Services\SelectOption;
use Mockery;

/**
 * Class SelectOptionTest
 */
class SelectOptionTest extends TestCase
{
    /**
     * 获取SelectOption实例
     *
     * @return Mockery\Mock|SelectOption
     */
    protected function mockSelectOption()
    {
        $repository = Mockery::mock(Repository::class);
        $repository->shouldReceive('selectOptions')->andReturn('success get select option info from repository!');

        $mock = Mockery::mock(SelectOption::class)->makePartial();
        $mock->shouldReceive('repository')->andReturn($repository);
        return $mock;
    }

    /**
     * 测试获取资源选项信息
     */
    public function testGetSelectOption()
    {
        $this->assertEquals('success get select option info from repository!', $this->mockSelectOption()->selectOptions());
    }
}
