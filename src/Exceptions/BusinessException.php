<?php

namespace Foris\LaExtension\Exceptions;

/**
 * Class BusinessException
 */
class BusinessException extends BaseException
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
        return config('app-ext.api_response_code.failure', \Foris\LaExtension\Http\Response::CODE_FAILURE);
    }
}
