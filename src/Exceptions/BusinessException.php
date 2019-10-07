<?php

namespace Foris\LaExtension\Exceptions;

use Foris\LaExtension\Http\Facade\Response;

/**
 * Class BusinessException
 */
class BusinessException extends BaseException
{
    /**
     * 构建异常响应结果
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        return Response::failure($this->getMessage(), $this->getData(), $this->getCode());
    }
}