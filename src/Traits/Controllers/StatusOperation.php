<?php /** @noinspection PhpUndefinedClassInspection */

namespace Foris\LaExtension\Traits\Controllers;

use Foris\LaExtension\Http\Facade\Response;
use Foris\LaExtension\Services\CrudService;
use Foris\LaExtension\Traits\Services\StatusOperation as ServiceStatusOperation;

/**
 * Trait StatusOperation
 */
trait StatusOperation
{
    /**
     * 获取资源管理服务实例
     *
     * @return     CrudService|ServiceStatusOperation
     */
    abstract public function service();

    /**
     * 启用资源信息
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Foris\LaExtension\Exceptions\BusinessException
     */
    public function enable($id)
    {
        return Response::success($this->service()->enable($id));
    }

    /**
     * 禁用资源信息
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Foris\LaExtension\Exceptions\BusinessException
     */
    public function disable($id)
    {
        return Response::success($this->service()->enable($id, false));
    }

    /**
     * 批量启用资源信息
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Foris\LaExtension\Exceptions\BusinessException
     */
    public function batchEnable()
    {
        $ids = request()->input('ids', []);
        return $this->service()->enable($ids) ? Response::success([]) : Response::failure();
    }

    /**
     * 批量禁用资源信息
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Foris\LaExtension\Exceptions\BusinessException
     */
    public function batchDisable()
    {
        $ids = request()->input('ids', []);
        return $this->service()->enable($ids, false) ? Response::success([]) : Response::failure();
    }
}
