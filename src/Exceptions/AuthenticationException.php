<?php

namespace Foris\LaExtension\Exceptions;

use Symfony\Component\HttpFoundation\Response as FoundationResponse;

/**
 * Class AuthenticationException
 */
class AuthenticationException extends BaseException
{
    /**
     * 获取默认调用的Response方法
     *
     * @return string
     */
    protected function getDefaultResponseMethod()
    {
        return 'unauthorized';
    }

    /**
     * 获取默认响应的状态码
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    protected function getDefaultResponseCode()
    {
        return config('app-ext.api_response_code.unauthorized', FoundationResponse::HTTP_UNAUTHORIZED);
    }
}
