<?php


namespace Foris\LaExtension\Exceptions;

use Throwable;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

/**
 * Class ParamsValidateException
 */
class ParamsValidateException extends BusinessException
{
    /**
     * PermissionForbiddenException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param null|Throwable $previous
     * @param array          $data
     */
    public function __construct(
        string $message = "",
        int $code = FoundationResponse::HTTP_UNPROCESSABLE_ENTITY,
        Throwable $previous = null,
        array $data = []
    ) {
        parent::__construct($message, $code, $previous, $data);
    }

    /**
     * 认证失败异常返回
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        $response = parent::render();
        $response->setStatusCode(FoundationResponse::HTTP_UNPROCESSABLE_ENTITY);
        return $response;
    }
}