<?php

namespace Foris\LaExtension\Traits\Repositories;

use Illuminate\Database\Eloquent\Model;
use Foris\LaExtension\Traits\Models\SelectOption as ModelSelectOption;

/**
 * Class SelectOption
 */
trait SelectOption
{
    /**
     * model实例
     *
     * @param bool $newInstance
     * @return Model|ModelSelectOption
     */
    abstract public function model($newInstance = false) : Model;

    /**
     * 查询实例
     *
     * @return mixed
     */
    abstract public function query();

    /**
     * 判断model是否支持选项操作
     *
     * @return bool
     */
    public function hasSelectOptionOperation()
    {
        $reflection = new \ReflectionObject($this->model());
        return in_array(ModelSelectOption::class, $reflection->getTraitNames());
    }

    /**
     * 获取资源选项信息
     *
     * @return mixed
     */
    public function selectOptions()
    {
        return $this->query()->select($this->model()->getSelectOptionKeys())->get();
    }
}