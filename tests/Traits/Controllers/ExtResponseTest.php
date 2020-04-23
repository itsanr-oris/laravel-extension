<?php

namespace Foris\LaExtension\Tests\Traits\Controllers;

use Foris\LaExtension\Http\Response;
use Foris\LaExtension\Tests\TestCase;
use Foris\LaExtension\Traits\Controllers\ExtResponse;
use Mockery;

/**
 * Class ExtResponseTest
 */
class ExtResponseTest extends TestCase
{
    /**
     * set up
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->singleton('app-ext.response', function () {
            return Mockery::mock(Response::class)
                ->shouldReceive([
                    'success' => 'success method have been called!',
                    'failure' => 'failure method have been called!',
                    'forbidden' => 'forbidden method have been called!',
                    'unauthorized' => 'unauthorized method have been called!',
                    'exception' => 'exception method have been called!',
                    'notFound' => 'not-found method have been called!'
                ])->getMock();
        });
    }

    /**
     * 获取ExtResponse实例
     *
     * @return Mockery\Mock|ExtResponse
     */
    protected function mockExtResponse()
    {
        return Mockery::mock(ExtResponse::class)->makePartial();
    }

    /**
     * 测试成功响应
     */
    public function testSuccessResponse()
    {
        $this->assertEquals('success method have been called!', $this->mockExtResponse()->success());
    }

    /**
     * 测试失败响应
     */
    public function testFailureResponse()
    {
        $this->assertEquals('failure method have been called!', $this->mockExtResponse()->failure());
    }

    /**
     * 测试未授权响应
     */
    public function testForbiddenResponse()
    {
        $this->assertEquals('forbidden method have been called!', $this->mockExtResponse()->forbidden());
    }

    /**
     * 测试未认证响应
     */
    public function testUnauthorizedResponse()
    {
        $this->assertEquals('unauthorized method have been called!', $this->mockExtResponse()->unauthorized());
    }

    /**
     * 测试异常响应
     */
    public function testExceptionResponse()
    {
        $this->assertEquals('exception method have been called!', $this->mockExtResponse()->exception());
    }

    /**
     * 测试
     */
    public function testNotFoundResponse()
    {
        $this->assertEquals('not-found method have been called!', $this->mockExtResponse()->notFound());
    }
}
