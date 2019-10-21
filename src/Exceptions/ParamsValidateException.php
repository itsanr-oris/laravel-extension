<?php

namespace Foris\LaExtension\Exceptions;

use Symfony\Component\HttpFoundation\Response as FoundationResponse;

/**
 * Class ParamsValidateException
 */
class ParamsValidateException extends BusinessException
{
    /**
     * 获取默认调用的Response方法
     *
     * @return string
     */
    protected function getDefaultResponseMethod()
    {
        return 'failure';
    }

    /**
     * 获取默认响应的状态码
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    protected function getDefaultResponseCode()
    {
        return config('app-ext.api_response_code.paramsValidException', FoundationResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * 参数校验失败响应结果，增加http状态码
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
