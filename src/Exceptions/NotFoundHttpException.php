<?php

namespace Foris\LaExtension\Exceptions;

use Throwable;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

/**
 * Class NotFoundHttpException
 */
class NotFoundHttpException extends BaseException
{
    /**
     * NotFoundHttpException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     * @param array          $data
     */
    public function __construct(
        string $message = "",
        int $code = FoundationResponse::HTTP_NOT_FOUND,
        Throwable $previous = null,
        array $data = []
    ) {
        parent::__construct($message, $code, $previous, $data);
    }

    /**
     * 找不到路由异常
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        $response = parent::render();
        $response->setStatusCode(FoundationResponse::HTTP_NOT_FOUND);
        return $response;
    }
}