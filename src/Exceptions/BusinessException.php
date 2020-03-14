<?php

namespace Foris\LaExtension\Exceptions;

use Foris\LaExtension\Http\Facade\Response;

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

    /**
     * 请求响应数据
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 异常信息转换为请求响应结果
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        $code = empty($this->getCode()) ? $this->getDefaultResponseCode() : $this->getCode();
        $method = $this->getDefaultResponseMethod();

        return Response::$method($this->getMessage() , $this->getData(), $code);
    }
}
