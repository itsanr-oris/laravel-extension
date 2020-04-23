<?php

namespace Foris\LaExtension\Tests\Traits\Controllers;

use Foris\LaExtension\Http\Response;
use Foris\LaExtension\Services\CrudService;
use Foris\LaExtension\Tests\TestCase;
use Foris\LaExtension\Traits\Controllers\SelectOption;
use Mockery;

/**
 * Class SelectionOptionTest
 */
class SelectionOptionTest extends TestCase
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
            return $mock;
        });
    }

    /**
     * 获取ResourceOperation实例
     *
     * @return Mockery\LegacyMockInterface|Mockery\MockInterface|SelectOption
     */
    public function mockSelectOption()
    {
        $service = Mockery::mock(CrudService::class);
        $service->shouldReceive('selectOptions')->andReturn('select-options method have been called!');

        return Mockery::mock(SelectOption::class)
            ->makePartial()->shouldReceive('service')->andReturn($service)->getMock();
    }

    /**
     * 测试获取下拉选项数据列表
     */
    public function testSelectOptions()
    {
        $this->assertEquals('select-options method have been called!', $this->mockSelectOption()->selectOptions());
    }
}
