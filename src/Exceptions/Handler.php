<?php

namespace Foris\LaExtension\Exceptions;

use Exception;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Exceptions\Handler as BaseExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

/**
 * Class Handler
 */
class Handler extends BaseExceptionHandler
{
    /**
     * @var ExceptionHandler
     */
    protected $handler;

    /**
     * Handler constructor.
     * @param ExceptionHandler $handler
     * @param Container        $container
     */
    public function __construct(ExceptionHandler $handler, Container $container)
    {
        $this->handler = $handler;
        parent::__construct($container);
    }

    /**
     * Report or log an exception.
     *
     * @param Exception $exception
     * @return mixed|void
     */
    public function report(Exception $exception)
    {
        if ($exception instanceof BaseException) {
            $exception->report();
            return ;
        }

        $this->handler->report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Exception                $exception
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof BaseException) {
            return $exception->render();
        }

        if ($exception instanceof ValidationException) {
            return (new ParamsValidateException(
                '参数校验异常', FoundationResponse::HTTP_UNPROCESSABLE_ENTITY, $exception, $exception->errors()
            ))->render();
        }

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return (new NotFoundHttpException('404 not found!'))->render();
        }

        // 对于不是BaseException的，封装成BaseException，避免错误信息暴露
        return (new ErrorException('系统正在开小差，请稍后重新尝试哦~', 1, $exception))->render();
    }
}