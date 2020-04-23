<?php /** @noinspection PhpUndefinedClassInspection */

namespace Foris\LaExtension\Traits\Controllers;

use Illuminate\Http\Request;
use Foris\LaExtension\Http\Facade\Response;
use Foris\LaExtension\Services\CrudService;

/**
 * Trait Resource
 */
trait ResourceOperation
{
    /**
     * 获取crud service实例
     *
     * @return CrudService
     */
    abstract public function service();

    /**
     * 获取数据列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return Response::success($this->service()->list(request()->query()));
    }

    /**
     * 获取数据详情
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        if ($detail = $this->service()->detail($id)) {
            return Response::success($detail);
        }

        return Response::notFound('获取详情失败，找不到指定资源信息!');
    }

    /**
     * 获取资源创建表单
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        return Response::success(['form' => []]);
    }

    /**
     * store/update参数校验
     *
     * @param Request $request
     * @return array
     */
    protected function params(Request $request)
    {
        return $request->all();
    }

    /**
     * 创建资源信息
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        if ($result = $this->service()->create($this->params($request))) {
            return Response::success($result);
        }

        return Response::failure('创建资源信息失败，请稍后重新尝试!');
    }

    /**
     * 获取资源编辑表单
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $detail = $this->service()->detail($id);
        return Response::success(['form' => [], 'data' => $detail ?? []]);
    }

    /**
     * 更新资源信息
     *
     * @param Request $request
     * @param         $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        if ($result = $this->service()->update($id, $this->params($request))) {
            return Response::success($result);
        }

        return Response::failure('更新资源信息失败，请稍后重新尝试!');
    }

    /**
     * 删除资源信息
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        return $this->service()->delete($id) ? Response::success([]) : Response::failure('操作失败，请稍后重新尝试!');
    }

    /**
     * 批量删除
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function batchDestroy()
    {
        $ids = request()->input('ids', []);
        return $this->service()->batchDelete($ids) ? Response::success([]) : Response::failure('操作失败，请稍后重新尝试!');
    }
}
