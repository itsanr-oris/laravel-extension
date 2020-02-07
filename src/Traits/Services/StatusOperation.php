<?php

namespace Foris\LaExtension\Traits\Services;

use Foris\LaExtension\Repositories\CrudRepository;
use Foris\LaExtension\Traits\Repositories\StatusOperation as RepositoryStatusOperation;

/**
 * Trait StatusOperation
 */
trait StatusOperation
{
    /**
     * 获取资源数据仓库
     *
     * @return CrudRepository|RepositoryStatusOperation
     */
    abstract public function repository();

    /**
     * 启/禁用资源信息
     *
     * @param      $ids
     * @param bool $enable
     * @return mixed
     * @throws \Foris\LaExtension\Exceptions\BusinessException
     */
    public function enable($ids, $enable = true)
    {
        return $this->repository()->enable($ids, $enable);
    }
}
