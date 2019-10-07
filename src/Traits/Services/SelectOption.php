<?php

namespace Foris\LaExtension\Traits\Services;

use Foris\LaExtension\Repositories\CrudRepository;
use Foris\LaExtension\Traits\Repositories\SelectOption as RepositorySelectOption;

/**
 * Trait SelectOption
 */
trait SelectOption
{
    /**
     * 获取资源数据仓库
     *
     * @return CrudRepository|RepositorySelectOption
     */
    abstract public function repository() : CrudRepository;

    /**
     * 获取资源选项信息
     *
     * @return mixed
     */
    public function selectOptions()
    {
        return $this->repository()->selectOptions();
    }
}