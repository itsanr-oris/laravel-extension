<?php

namespace Foris\LaExtension\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Foris\LaExtension\Exceptions\ErrorException;

/**
 * Class CrudRepository
 */
abstract class CrudRepository extends Repository
{
    /**
     * 是否启用软删除查询
     *
     * @var bool
     */
    protected $withTrashed = false;

    /**
     * 是否检测软删除
     *
     * @var bool
     */
    protected $checkSoftDelete = false;

    /**
     * ResourceRepository constructor.
     */
    public function __construct()
    {
        $this->checkSoftDelete(config('app-ext.check_model_soft_delete', false));
    }

    /**
     * 获取数据模型
     *
     * @param bool $newInstance
     * @return Model|SoftDeletes
     */
    abstract public function model($newInstance = false) : Model;

    /**
     * 操作的数据含软删除数据
     *
     * @param bool $withTrashed
     * @return $this
     * @throws ErrorException
     */
    public function withTrashed($withTrashed = true)
    {
        if (!$this->modelCanSoftDelete()) {
            throw new ErrorException(sprintf('Model[%s] does not support soft-delete!', get_class($this->model())));
        }

        $this->withTrashed = $withTrashed;
        return $this;
    }

    /**
     * 检测软删除
     *
     * @param bool $check
     * @return $this
     */
    public function checkSoftDelete($check = true)
    {
        $this->checkSoftDelete = $check;
        return $this;
    }

    /**
     * 检查模型是否支持软删除
     *
     * @return bool
     */
    protected function modelCanSoftDelete()
    {
        $reflection = new \ReflectionObject($this->model());
        return in_array(SoftDeletes::class, $reflection->getTraitNames());
    }

    /**
     * 获取数据库query builder实例
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        if ($this->withTrashed && $this->modelCanSoftDelete()) {
            return call_user_func_array([$this->model(), 'withTrashed'], []);
        }

        return $this->model()->newQuery();
    }

    /**
     * 获取全部数据
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->query()->get();
    }

    /**
     * 获取列表查询实例
     *
     * @param array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function listQuery(array $filter = [])
    {
        $query = $this->query();

        if (!empty($filter['select'])) {
            $query->select($filter['select']);
        }

        return $query->orderBy($this->model()->getKeyName(), 'desc');
    }

    /**
     * 获取数据列表
     *
     * @param array $filter
     * @param bool  $simplePaginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Pagination\Paginator
     */
    public function list(array $filter, $simplePaginate = false)
    {
        $filter['page_index'] = $filter['page_index'] ?? 1;
        $filter['page_size'] = $filter['page_size'] ?? 20;

        $query = $this->listQuery($filter);

        if ($simplePaginate) {
            return $query->simplePaginate($filter['page_size'], ['*'], 'page_index', $filter['page_index']);
        }
        return $query->paginate($filter['page_size'], ['*'], 'page_index', $filter['page_index']);
    }

    /**
     * 获取数据详情
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|Model|null|object
     */
    public function detail($id)
    {
        return $this->query()->where($this->model()->getKeyName(), $id)->first();
    }

    /**
     * 创建数据
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Builder|Model
     */
    public function create(array $data)
    {
        return $this->query()->create($data);
    }

    /**
     * 删除数据
     *
     * @param $id
     * @return mixed
     * @throws ErrorException
     */
    public function delete($id)
    {
        if ($this->checkSoftDelete && !$this->modelCanSoftDelete()) {
            throw new ErrorException(sprintf('Model[%s] does not support soft-delete!', get_class($this->model())));
        }

        return $this->query()->where($this->model()->getKeyName(), $id)->delete();
    }

    /**
     * 更新数据
     *
     * @param       $id
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Builder|Model|null|object
     */
    public function update($id, array $data)
    {
        $model = $this->query()->where($this->model()->getKeyName(), $id)->first();
        return empty($model) ? null : ($model->fill($data)->save() ? $model : null);
    }

    /**
     * 开启事务
     *
     * @param \Closure $callback
     * @param int      $attempts
     * @return mixed
     * @throws \Throwable
     */
    public function transaction(\Closure $callback, $attempts = 1)
    {
        return $this->model()->getConnection()->transaction($callback, $attempts);
    }

    /**
     * 批量获取数据详情
     *
     * @param array $ids
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function batchDetail(array $ids = [])
    {
        return $this->query()->whereIn($this->model()->getKeyName(), $ids)->get();
    }

    /**
     * 批量删除
     *
     * @param array $ids
     * @return mixed
     */
    public function batchDelete(array $ids = [])
    {
        return $this->query()->whereIn($this->model()->getKeyName(), $ids)->delete();
    }
}
