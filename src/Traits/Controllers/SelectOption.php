<?php

namespace Foris\LaExtension\Traits\Controllers;

use Foris\LaExtension\Http\Facade\Response;
use Foris\LaExtension\Services\CrudService;
use Foris\LaExtension\Traits\Services\SelectOption as ServiceSelectOption;

/**
 * Class SelectOptions
 */
trait SelectOption
{
    /**
     * 获取资源管理服务实例
     *
     * @return     CrudService|ServiceSelectOption
     */
    abstract public function service() : CrudService;

    /**
     * 获取资源选项信息
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function selectOptions()
    {
        return Response::success($this->service()->selectOptions());
    }
}