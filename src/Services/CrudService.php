<?php

namespace Foris\LaExtension\Services;

use Foris\LaExtension\Repositories\CrudRepository;
use Illuminate\Support\Arr;

/**
 * Class CrudService
 */
abstract class CrudService extends Service
{
    /**
     * 获取资源数据仓库
     *
     * @return CrudRepository
     */
    abstract public function repository();

    /**
     * 获取数据列表
     *
     * @param array $filter
     * @param bool  $simplePaginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Pagination\Paginator
     */
    public function list(array $filter, $simplePaginate = false)
    {
        return $this->repository()->list($filter, $simplePaginate);
    }

    /**
     * 获取数据详情
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */
    public function detail($id)
    {
        return $this->repository()->detail($id);
    }

    /**
     * 创建数据
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        return $this->save($data);
    }

    /**
     * 更新数据
     *
     * @param       $id
     * @param array $data
     * @return bool|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */
    public function update($id, array $data)
    {
        $pk = $this->repository()->model()->getKeyName();
        return $this->save(array_merge($data, [$pk => $id]));
    }

    /**
     * 保存数据
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */
    public function save(array $data)
    {
        $pk = $this->repository()->model()->getKeyName();

        return empty($data[$pk])
            ? $this->repository()->create($data)
            : $this->repository()->update($data[$pk], Arr::except($data, [$pk]));
    }

    /**
     * 删除数据
     *
     * @param $id
     * @return mixed
     * @throws \Foris\LaExtension\Exceptions\ErrorException
     */
    public function delete($id)
    {
        return $this->repository()->delete($id);
    }

    /**
     * 批量删除数据
     *
     * @param array $ids
     * @return mixed
     * @throws \Throwable
     */
    public function batchDelete($ids = [])
    {
        return $this->repository()->transaction(function () use ($ids) {
            collect($ids)->each([$this, 'delete']);
            return true;
        });
    }
}
