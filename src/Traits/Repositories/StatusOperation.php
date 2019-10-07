<?php

namespace Foris\LaExtension\Traits\Repositories;

use Foris\LaExtension\Exceptions\BusinessException;
use Foris\LaExtension\Traits\Models\StatusDefinition;
use Illuminate\Database\Eloquent\Model;
use Foris\LaExtension\Traits\Models\StatusOperation as ModelStatusOperation;

/**
 * Trait StatusOperation
 */
trait StatusOperation
{
    /**
     * model实例
     *
     * @param bool $newInstance
     * @return Model|ModelStatusOperation
     */
    abstract public function model($newInstance = false) : Model;

    /**
     * 查询实例
     *
     * @return mixed
     */
    abstract public function query();

    /**
     * 判断model是否支持状态操作
     *
     * @return bool
     */
    public function hasStatusOperation()
    {
        $reflection = new \ReflectionObject($this->model());
        return in_array(ModelStatusOperation::class, $reflection->getTraitNames());
    }

    /**
     * 启/禁用状态信息
     *
     * @param      $ids
     * @param bool $enable
     * @return mixed
     * @throws BusinessException
     */
    public function enable($ids, $enable = true)
    {
        if (!$this->hasStatusOperation()) {
            throw new BusinessException('不支持状态变更操作!!');
        }

        $ids = is_array($ids) ? $ids : (array) $ids;
        $status = $enable ? StatusDefinition::STATUS_ENABLE : StatusDefinition::STATUS_DISABLE;
        return $this->query()->whereIn($this->model()->getKeyName(), $ids)->update([$this->model()->getStatusKeyName() => $status]);
    }
}