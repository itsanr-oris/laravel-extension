<?php

namespace Foris\LaExtension\Tests\Exceptions;

use Foris\LaExtension\Exceptions\AuthenticationException;
use Foris\LaExtension\Exceptions\BaseException;
use Foris\LaExtension\Exceptions\BusinessException;
use Foris\LaExtension\Exceptions\Handler;
use Foris\LaExtension\Exceptions\NotFoundHttpException;
use Foris\LaExtension\Exceptions\ParamsValidateException;
use Foris\LaExtension\Exceptions\PermissionForbiddenException;
use Foris\LaExtension\Tests\TestCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException as SymfonyNotFoundHttpException;

/**
 * Class ExceptionTest
 */
class ExceptionTest extends TestCase
{
    /**
     * 获取BaseException实例
     *
     * @return BaseException
     */
    protected function makeBaseException()
    {
        $previous = new \Exception('previous exception');
        return new BaseException('base exception', null, $previous, ['key' => 'value']);
    }

    /**
     * 测试异常响应功能
     */
    public function testBaseExceptionRender()
    {
        // 调试模式下
        $this->assertTrue(env('APP_DEBUG', false));
        $response = $this->makeBaseException()->render();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(FoundationResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        $data = $response->getData(true);
        $code = config('app-ext.api_response_code.exception', FoundationResponse::HTTP_INTERNAL_SERVER_ERROR);

        $this->assertEquals($code, $data['code']);
        $this->assertEquals('base exception', $data['message']);
        $this->assertArrayHasKey('exception', $data['data']);
        $this->assertEquals('value', $data['data']['key']);

        // 转换到非调试模式
        $_ENV['APP_DEBUG'] = false;
        $this->assertFalse(env('APP_DEBUG', false));
        $response = $this->makeBaseException()->render();

        $data = $response->getData(true);
        $this->assertEquals('系统正在开小差，请稍后重新尝试哦~', $data['message']);
        $this->assertArrayNotHasKey('exception', $data['data']);
        $_ENV['APP_DEBUG'] = true;
    }

    /**
     * 测试异常日志功能
     */
    public function testBaseExceptionReport()
    {
        // 关闭调试模式，让getData获取到原始数据
        $_ENV['APP_DEBUG'] = false;

        $exception = $this->makeBaseException();
        $exception->report();

        $expectedMessage = $exception->getMessage();
        $expectedContext = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'data' => $exception->getData(),
            'request' => [
                'url' => request()->url(),
                'input' => request()->input(),
                'ip' => request()->getClientIp(),
            ],
            'exception' => $exception,
        ];

        $this->getLogger()->assertLogged('error', function ($message, $context) use ($expectedMessage, $expectedContext) {
            $this->assertEquals($expectedMessage, $message);
            $this->assertEquals($expectedContext, $context);

            return true;
        });

        $_ENV['APP_DEBUG'] = true;
    }

    /**
     * 测试认证异常
     */
    public function testAuthenticationException()
    {
        $response = (new AuthenticationException('authentication exception'))->render();
        $data = $response->getData(true);
        $code = config('app-ext.api_response_code.unauthorized', FoundationResponse::HTTP_UNAUTHORIZED);

        $this->assertEquals($code, $data['code']);
        $this->assertEquals('authentication exception', $data['message']);
        $this->assertEquals(FoundationResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * 测试业务异常
     */
    public function testBusinessException()
    {
        $response = (new BusinessException('business exception'))->render();
        $data = $response->getData(true);
        $code = config('app-ext.api_response_code.failure', \Foris\LaExtension\Http\Response::CODE_FAILURE);

        $this->assertEquals($code, $data['code']);
        $this->assertEquals('business exception', $data['message']);
        $this->assertEquals(FoundationResponse::HTTP_OK, $response->getStatusCode());
    }

    /**
     * 测试Error异常
     */
    public function testErrorException()
    {
        $this->assertTrue(true);
    }

    /**
     * 测试 not found 异常
     */
    public function testNotFoundHttpException()
    {
        $response = (new NotFoundHttpException('not found exception'))->render();
        $data = $response->getData(true);
        $code = config('app-ext.api_response_code.notFound', FoundationResponse::HTTP_NOT_FOUND);

        $this->assertEquals($code, $data['code']);
        $this->assertEquals('not found exception', $data['message']);
        $this->assertEquals(FoundationResponse::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * 测试参数校验异常
     */
    public function testParamsValidateException()
    {
        $response = (new ParamsValidateException('params validate exception'))->render();
        $data = $response->getData(true);
        $code = config('app-ext.api_response_code.paramsValidException', FoundationResponse::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertEquals($code, $data['code']);
        $this->assertEquals('params validate exception', $data['message']);
        $this->assertEquals(FoundationResponse::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    public function testPermissionForbiddenException()
    {
        $response = (new PermissionForbiddenException('permission forbidden exception'))->render();
        $data = $response->getData(true);
        $code = config('app-ext.api_response_code.forbidden', FoundationResponse::HTTP_FORBIDDEN);

        $this->assertEquals($code, $data['code']);
        $this->assertEquals('permission forbidden exception', $data['message']);
        $this->assertEquals(FoundationResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * 测试启用自定义exception handler
     */
    public function testEnableCustomExceptionHandler()
    {
        $this->assertTrue($this->getConfig()->get('app-ext.handle_exception'));
        $this->assertInstanceOf(Handler::class, $this->app->get(ExceptionHandler::class));
    }

    /**
     * 获取BaseException mock实例
     *
     * @return \Mockery\LegacyMockInterface|\Mockery\MockInterface|BaseException
     */
    protected function getBaseExceptionMock()
    {
        $mock = \Mockery::mock(BaseException::class);

        $mock->shouldReceive('report');
        $mock->shouldReceive('render')->andReturn(new JsonResponse());

        return $mock;
    }

    /**
     * 获取ExceptionHandler实例
     *
     * @return \Mockery\LegacyMockInterface|\Mockery\MockInterface|ExceptionHandler
     */
    protected function mockExceptionHandler()
    {
        return \Mockery::mock(ExceptionHandler::class)->shouldReceive('report')->getMock();
    }

    /**
     * 测试通过 handler 记录异常日志
     */
    public function testExceptionReportViaExceptionHandler()
    {
        $handler = new Handler($this->mockExceptionHandler(), $this->app);

        // 测试BaseException异常日志记录，调用BaseException的report方法
        $handler->report($this->getBaseExceptionMock());

        // 测试非BaseException异常日志记录，调用ParentHandler的report方法
        $handler->report(new \Exception());
    }

    /**
     * 获取ValidationException实例
     *
     * @return \Mockery\LegacyMockInterface|\Mockery\MockInterface|ValidationException
     */
    protected function mockValidationException()
    {
        return \Mockery::mock(ValidationException::class)->shouldReceive('errors')->andReturn([])->getMock();
    }

    /**
     * 获取SymfonyNotFoundHttpException实例
     *
     * @return NotFoundHttpException|\Mockery\LegacyMockInterface|\Mockery\MockInterface|SymfonyNotFoundHttpException
     */
    protected function mockNotFoundException()
    {
        return \Mockery::mock(SymfonyNotFoundHttpException::class);
    }

    /**
     * 测试通过 handler 进行异常响应
     */
    public function testExceptionRenderViaExceptionHandler()
    {
        $handler = new Handler($this->mockExceptionHandler(), $this->app);

        // 测试BaseException异常响应，调用BaseException的render方法
        $this->assertInstanceOf(JsonResponse::class, $handler->render(request(), $this->getBaseExceptionMock()));

        // 测试 ValidationException 异常响应
        $response = $handler->render(request(), $this->mockValidationException());
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(FoundationResponse::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());

        // 测试 NotFoundHttpException 异常响应
        $response = $handler->render(request(), $this->mockNotFoundException());
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(FoundationResponse::HTTP_NOT_FOUND, $response->getStatusCode());

        // 测试其他异常响应
        $response = $handler->render(request(), new \Exception());
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(FoundationResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}
